<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;
use App\Models\Group;
use App\Models\Payment;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Storage;

class PaymentsController extends Controller
{
    public function index()
    {
        $userId = request('user_id');
        $groupId = request('group_id');

        $payments = Payment::query()
            ->with('user', 'group.course')
            ->filterByUser($userId)
            ->filterByGroup($groupId)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('payments.index', [
            'title' => 'Платежи',
            'payments' => $payments,
            'clients' => User::clients(),
            'groups' => Group::with('course')->get(),
            'filters' => [
                'user_id' => $userId,
                'group_id' => $groupId,
            ],
        ]);
    }

    public function create()
    {
        // Подсчитываем количество платежей для каждой пары студент-группа
        $paymentCounts = [];
        $payments = Payment::selectRaw('user_id, group_id, COUNT(*) as count')
            ->groupBy('user_id', 'group_id')
            ->get();

        foreach ($payments as $payment) {
            $paymentCounts[$payment->user_id][$payment->group_id] = $payment->count;
        }

        return view('payments.create', [
            'title' => 'Создание платежа',
            'clients' => User::clients(),
            'groups' => Group::with('course')->get(),
            'paymentCounts' => $paymentCounts,
        ]);
    }

    public function store(PaymentRequest $request, PaymentService $paymentService)
    {
        $validated = $request->validated();

        // Проверка лимита платежей
        if (! $paymentService->checkPaymentLimit($validated['user_id'], $validated['group_id'])) {
            return back()
                ->withInput()
                ->with('error', 'Лимит платежей (3) для этой группы исчерпан');
        }

        // Проверка суммы (только для 3-го платежа)
        $existingPayments = Payment::where('user_id', $validated['user_id'])
            ->where('group_id', $validated['group_id'])
            ->count();

        if ($existingPayments === 2) {
            $error = $paymentService->checkPaymentAmount($validated['user_id'], $validated['group_id'], (float) $validated['amount']);
            if ($error) {
                return back()
                    ->withInput()
                    ->with('error', $error['error']);
            }
        }

        // Обработка файла чека
        [$receiptPath, $receiptName] = [null, null];
        if ($request->hasFile('receipt')) {
            [$receiptPath, $receiptName] = $paymentService->handleReceiptUpload($request->file('receipt'));
        }

        Payment::create([
            ...$validated,
            'receipt_path' => $receiptPath,
            'receipt_name' => $receiptName,
        ]);

        return redirect()
            ->route('payments.index')
            ->with('success', 'Платёж успешно создан');
    }

    public function edit(Payment $payment)
    {
        return view('payments.edit', [
            'title' => 'Редактирование платежа',
            'payment' => $payment,
            'groups' => $payment->user->studentGroups()->with('course')->get(),
        ]);
    }

    public function update(PaymentRequest $request, Payment $payment, PaymentService $paymentService)
    {
        $validated = $request->validated();

        // Проверка лимита при смене группы
        if ($payment->group_id != $validated['group_id']) {
            if (! $paymentService->checkPaymentLimit($payment->user_id, $validated['group_id'], $payment->id)) {
                return back()
                    ->withInput()
                    ->with('error', 'Лимит платежей (3) для этой группы исчерпан');
            }
        }

        // Проверка суммы
        $error = $paymentService->checkPaymentAmount($payment->user_id, $validated['group_id'], (float) $validated['amount'], $payment->id);
        if ($error) {
            return back()
                ->withInput()
                ->with('error', $error['error']);
        }

        // Обработка файла чека
        [$receiptPath, $receiptName] = [$payment->receipt_path, $payment->receipt_name];
        if ($request->hasFile('receipt')) {
            if ($payment->receipt_path) {
                Storage::disk('local')->delete($payment->receipt_path);
            }
            [$receiptPath, $receiptName] = $paymentService->handleReceiptUpload($request->file('receipt'));
        }

        $payment->update([
            ...$validated,
            'receipt_path' => $receiptPath,
            'receipt_name' => $receiptName,
        ]);

        return redirect()
            ->route('payments.index')
            ->with('success', 'Платёж успешно обновлён');
    }

    public function downloadReceipt(Payment $payment)
    {
        // Проверка доступа: админ/менеджер или владелец платежа
        $user = auth()->user();
        if (! $user->isAdmin() && ! $user->isManager() && $payment->user_id !== $user->id) {
            abort(403);
        }

        if (! $payment->receipt_path) {
            abort(404);
        }

        return Storage::disk('local')->download($payment->receipt_path, $payment->receipt_name);
    }

    public function destroy(Payment $payment)
    {
        // Удалить файл чека
        if ($payment->receipt_path) {
            Storage::disk('local')->delete($payment->receipt_path);
        }

        $payment->delete();

        return redirect()
            ->route('payments.index')
            ->with('success', 'Платёж успешно удалён');
    }
}
