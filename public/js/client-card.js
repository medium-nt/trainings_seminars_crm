$(document).ready(function() {
    // Показать/скрыть блок карточки компании в "Мои документы"
    function toggleCompanyCardBlock() {
        const isCompany = $('input[name="payer_type"]:checked').val() === 'company';

        // Показываем/скрываем блок в "Мои документы"
        $('.company-card-document-block').toggle(isCompany);
    }

    $('input[name="payer_type"]').on('change', function() {
        const newValue = $(this).val();

        // Если выбрали "Лично" и есть загруженная карточка — удаляем её
        if (newValue === 'self' && $('.btn-delete-company-card').length > 0) {
            if (confirm('При выборе "Лично" карточка компании будет удалена. Продолжить?')) {
                const deleteButton = $('.btn-delete-company-card').first();
                const formData = new FormData();
                formData.append('_token', deleteButton.data('token'));
                formData.append('_method', 'DELETE');

                fetch(deleteButton.data('url'), {
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
            } else {
                // Отмена — возвращаем выбор на "Компания"
                $('input[name="payer_type"][value="company"]').prop('checked', true);
                return false;
            }
        }

        toggleCompanyCardBlock();
    });

    // Инициализация при загрузке
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

    // Удаление скана почтового документа
    $('.btn-delete-postal-doc').on('click', function() {
        if (!confirm('Вы уверены, что хотите удалить скан документа?')) {
            return;
        }

        const button = $(this);
        const formData = new FormData();
        formData.append('_token', button.data('token'));
        formData.append('_method', 'DELETE');
        if (button.data('user-id')) {
            formData.append('user_id', button.data('user-id'));
        }

        fetch(button.data('url'), {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(() => {
            window.location.reload();
        })
        .catch(() => {
            window.location.reload();
        });
    });
});
