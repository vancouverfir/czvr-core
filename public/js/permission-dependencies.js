(function () {
    function rolesGiving(form, slug) {
        return Array.from(form.querySelectorAll('input[name="roles[]"]:checked')).filter(function (role) {
            return JSON.parse(role.dataset.permissions || '[]').indexOf(slug) !== -1;
        });
    }

    function isProvided(form, slug) {
        var direct = form.querySelector('input[name="permissions[]"][value="' + slug + '"]');

        return (direct && direct.checked) || rolesGiving(form, slug).length > 0;
    }

    function listNames(names) {
        return names.length > 1 ? names.slice(0, -1).join(', ') + ' and ' + names[names.length - 1] : names[0];
    }

    function givenTag(input) {
        var tag = input.parentElement.querySelector('.permission-given');

        if (!tag) {
            tag = document.createElement('small');
            tag.className = 'permission-needs permission-given';
            tag.hidden = true;
            input.parentElement.appendChild(tag);
        }

        return tag;
    }

    function sync(form) {
        form.querySelectorAll('input[name="permissions[]"]').forEach(function (input) {
            var needs = input.parentElement.querySelector('.permission-needs:not(.permission-given)');

            if (input.dataset.locked === undefined) {
                input.dataset.locked = input.disabled ? '1' : '0';
            }

            var givenBy = rolesGiving(form, input.value).map(function (role) {
                return form.querySelector('label[for="' + role.id + '"]').textContent.trim();
            });

            if (givenBy.length) {
                if (input.dataset.given !== '1') {
                    input.dataset.given = '1';
                    input.dataset.direct = input.checked ? '1' : '0';
                }

                input.checked = true;
                input.disabled = true;
                givenTag(input).textContent = '(already given by the ' + listNames(givenBy) + (givenBy.length > 1 ? ' roles)' : ' role)');
                givenTag(input).hidden = false;

                if (needs) {
                    needs.hidden = true;
                }

                return;
            }

            if (input.dataset.given === '1') {
                input.dataset.given = '0';
                input.checked = input.dataset.direct === '1';
                input.disabled = input.dataset.locked === '1';
                givenTag(input).hidden = true;
            }

            if (!input.dataset.requires) {
                return;
            }

            var met = JSON.parse(input.dataset.requires).every(function (slug) {
                return isProvided(form, slug);
            });

            if (needs) {
                needs.hidden = met;
            }

            if (input.dataset.locked === '1') {
                return;
            }

            if (!met) {
                input.checked = false;
            }

            input.disabled = !met;
        });
    }

    document.querySelectorAll('input[name="permissions[]"][data-requires], input[name="roles[]"][data-permissions]').forEach(function (input) {
        sync(input.closest('form'));
    });

    document.addEventListener('change', function (event) {
        var form = event.target.closest('form');

        if (form && event.target.matches('input[name="permissions[]"], input[name="roles[]"]')) {
            sync(form);
        }
    });

    document.addEventListener('submit', function (event) {
        event.target.querySelectorAll('input[name="permissions[]"][data-given="1"]').forEach(function (input) {
            input.checked = input.dataset.direct === '1';
            input.disabled = input.dataset.locked === '1';
        });
    });
}());
