$(document).ready(function() {
    // Показать/скрыть блок карточки компании
    function toggleCompanyCardBlock() {
        if ($('input[name="payer_type"]:checked').val() === 'company') {
            $('.company-card-block').slideDown();
        } else {
            $('.company-card-block').slideUp();
        }
    }

    $('input[name="payer_type"]').on('change', toggleCompanyCardBlock);

    // Инициализация при загрузке (учитывает old() после ошибки валидации)
    toggleCompanyCardBlock();

    // Удаление карточки компании
    $('.btn-delete-company-card').on('click', function() {
        if (!confirm('Вы уверены, что хотите удалить карточку компании?')) {
            return;
        }

        const button = $(this);
        const formData = new FormData();
        formData.append('_token', button.data('token'));
        formData.append('_method', 'DELETE');

        fetch(button.data('url'), {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.redirect) {
                window.location.href = data.redirect;
            }
        })
        .catch(() => {
            window.location.reload();
        });
    });
});
