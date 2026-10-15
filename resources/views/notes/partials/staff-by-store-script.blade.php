@once
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('select.js-staff-by-store').forEach(function (staffSelect) {
            const storeSelect = document.querySelector(staffSelect.dataset.storeSelect || '#store_id');
            if (!storeSelect) {
                return;
            }

            const emptyLabel = staffSelect.dataset.emptyLabel || '-';
            const showAllWhenEmpty = staffSelect.dataset.showAllWhenEmpty === '1';
            const staffOptions = Array.from(staffSelect.querySelectorAll('option[data-store]')).map(function (option) {
                return {
                    value: option.value,
                    label: option.textContent,
                    storeId: String(option.dataset.store || ''),
                };
            });

            const rebuildStaffOptions = function () {
                const storeId = String(storeSelect.value || '');
                const currentValue = staffSelect.value;
                let keepValue = '';

                staffSelect.innerHTML = '';

                const emptyOption = document.createElement('option');
                emptyOption.value = '';
                emptyOption.textContent = emptyLabel;
                staffSelect.appendChild(emptyOption);

                staffOptions.forEach(function (staff) {
                    if (storeId === '') {
                        if (! showAllWhenEmpty) {
                            return;
                        }
                    } else if (staff.storeId !== storeId) {
                        return;
                    }

                    const option = document.createElement('option');
                    option.value = staff.value;
                    option.textContent = staff.label;
                    option.dataset.store = staff.storeId;
                    if (staff.value === currentValue) {
                        keepValue = staff.value;
                    }
                    staffSelect.appendChild(option);
                });

                staffSelect.value = keepValue;
            };

            storeSelect.addEventListener('change', function () {
                rebuildStaffOptions();

                if (staffSelect.dataset.submitOnStoreChange === '1' && storeSelect.form) {
                    storeSelect.form.submit();
                }
            });

            rebuildStaffOptions();
        });
    });
</script>
@endpush
@endonce
