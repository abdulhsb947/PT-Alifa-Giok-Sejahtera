@extends('layouts.direktur')

@section('content')

<div class="max-w-xl mx-auto bg-white p-6 rounded-xl shadow">

    <h2 class="text-xl font-bold mb-4">
        Tambah Pengguna
    </h2>

    <form method="POST"
          action="{{ route('direktur.users.store') }}"
          novalidate>

        @csrf

        {{-- NAMA --}}
        <div class="mb-4">
            <label class="block mb-1 font-medium">
                Nama
            </label>

            <input
                type="text"
                name="name"
                id="name"
                class="w-full border rounded p-2"
                placeholder="Masukkan nama lengkap">
        </div>

        {{-- EMAIL --}}
        <div class="mb-4">

            <label class="block mb-1 font-medium">
                Email
            </label>

            <input
                type="email"
                name="email"
                id="email"
                class="w-full border rounded p-2"
                placeholder="contoh@email.com">

            <p id="email-error"
               class="text-red-500 text-sm mt-1 hidden">

                Format email tidak valid.

            </p>

        </div>

        {{-- PASSWORD --}}
        <div class="mb-4">

            <label class="block mb-1 font-medium">
                Password
            </label>

            <div class="relative">

                <input
                    type="password"
                    name="password"
                    id="password"
                    class="w-full border rounded p-2 pr-12"
                    placeholder="Contoh: Admin@123">

                <button
                    type="button"
                    onclick="togglePassword('password')"
                    class="absolute right-3 top-1/2 -translate-y-1/2">

                    👁️

                </button>

            </div>

            <div class="mt-3 text-sm space-y-1">

                <div id="rule-length" class="text-red-500">
                    ❌ Minimal 8 karakter
                </div>

                <div id="rule-upper" class="text-red-500">
                    ❌ Huruf besar (A-Z)
                </div>

                <div id="rule-lower" class="text-red-500">
                    ❌ Huruf kecil (a-z)
                </div>

                <div id="rule-number" class="text-red-500">
                    ❌ Angka (0-9)
                </div>

                <div id="rule-symbol" class="text-red-500">
                    ❌ Simbol (!@#$%^&*)
                </div>

            </div>

        </div>

        {{-- KONFIRMASI PASSWORD --}}
        <div class="mb-4">

            <label class="block mb-1 font-medium">
                Konfirmasi Password
            </label>

            <div class="relative">

                <input
                    type="password"
                    name="password_confirmation"
                    id="password_confirmation"
                    class="w-full border rounded p-2 pr-12"
                    placeholder="Ulangi password">

                <button
                    type="button"
                    onclick="togglePassword('password_confirmation')"
                    class="absolute right-3 top-1/2 -translate-y-1/2">

                    👁️

                </button>

            </div>

            <p id="confirm-error"
               class="text-red-500 text-sm mt-1 hidden">

                Password tidak sama

            </p>

        </div>

        {{-- ROLE --}}
        <div class="mb-4">

            <label class="block mb-1 font-medium">
                Role
            </label>

            <select
                name="role"
                id="role"
                class="w-full border rounded p-2">

                <option value="">
                    -- Pilih Role --
                </option>

                <option value="admin">
                    Admin
                </option>

                <option value="direktur">
                    Direktur
                </option>

            </select>

        </div>

        {{-- BUTTON --}}
        <button
            id="submitBtn"
            type="submit"
            disabled
            class="bg-gray-400 text-white px-4 py-2 rounded cursor-not-allowed">

            Simpan

        </button>

    </form>

</div>


<script>

// ========================
// SHOW / HIDE PASSWORD
// ========================

function togglePassword(id)
{
    const input =
        document.getElementById(id);

    input.type =
        input.type === 'password'
        ? 'text'
        : 'password';
}


// ========================
// ELEMENT
// ========================

const nameInput =
    document.getElementById('name');

const emailInput =
    document.getElementById('email');

const passwordInput =
    document.getElementById('password');

const confirmInput =
    document.getElementById('password_confirmation');

const roleInput =
    document.getElementById('role');

const submitBtn =
    document.getElementById('submitBtn');

const emailError =
    document.getElementById('email-error');

const confirmError =
    document.getElementById('confirm-error');


// ========================
// UPDATE PASSWORD RULE
// ========================

function updateRule(id, valid)
{
    const el =
        document.getElementById(id);

    if(valid)
    {
        el.innerHTML =
            el.innerHTML.replace(
                '❌',
                '✅'
            );

        el.classList.remove(
            'text-red-500'
        );

        el.classList.add(
            'text-green-600'
        );
    }
    else
    {
        el.innerHTML =
            el.innerHTML.replace(
                '✅',
                '❌'
            );

        el.classList.remove(
            'text-green-600'
        );

        el.classList.add(
            'text-red-500'
        );
    }
}


// ========================
// PASSWORD CHECK
// ========================

passwordInput.addEventListener(
    'input',
    function()
{
    let value =
        this.value;

    updateRule(
        'rule-length',
        value.length >= 8
    );

    updateRule(
        'rule-upper',
        /[A-Z]/.test(value)
    );

    updateRule(
        'rule-lower',
        /[a-z]/.test(value)
    );

    updateRule(
        'rule-number',
        /[0-9]/.test(value)
    );

    updateRule(
        'rule-symbol',
        /[@$!%*#?&]/.test(value)
    );

    validateForm();
});


// ========================
// EMAIL CHECK
// ========================

emailInput.addEventListener(
    'input',
    function()
{
    if(
        emailInput.value !== '' &&
        !emailInput.validity.valid
    )
    {
        emailError.classList.remove(
            'hidden'
        );

        emailInput.classList.add(
            'border-red-500'
        );

        emailInput.classList.remove(
            'border-green-500'
        );
    }
    else
    {
        emailError.classList.add(
            'hidden'
        );

        emailInput.classList.remove(
            'border-red-500'
        );

        if(
            emailInput.value !== ''
        )
        {
            emailInput.classList.add(
                'border-green-500'
            );
        }
    }

    validateForm();
});


// ========================
// CONFIRM PASSWORD CHECK
// ========================

confirmInput.addEventListener(
    'input',
    validateForm
);


// ========================
// MAIN VALIDATION
// ========================

function validateForm()
{
    let valid = true;

    // NAMA
    if(
        nameInput.value.trim() === ''
    )
    {
        valid = false;
    }

    // EMAIL
    if(
        emailInput.value.trim() === '' ||
        !emailInput.validity.valid
    )
    {
        valid = false;
    }

    // PASSWORD
    const passwordValid =

        passwordInput.value.length >= 8 &&

        /[A-Z]/.test(
            passwordInput.value
        ) &&

        /[a-z]/.test(
            passwordInput.value
        ) &&

        /[0-9]/.test(
            passwordInput.value
        ) &&

        /[@$!%*#?&]/.test(
            passwordInput.value
        );

    if(!passwordValid)
    {
        valid = false;
    }

    // KONFIRMASI PASSWORD
    if(
        confirmInput.value === '' ||
        confirmInput.value !==
        passwordInput.value
    )
    {
        confirmError.classList.remove(
            'hidden'
        );

        valid = false;
    }
    else
    {
        confirmError.classList.add(
            'hidden'
        );
    }

    // ROLE
    if(
        roleInput.value === ''
    )
    {
        valid = false;
    }

    // BUTTON
    if(valid)
    {
        submitBtn.disabled = false;

        submitBtn.classList.remove(
            'bg-gray-400',
            'cursor-not-allowed'
        );

        submitBtn.classList.add(
            'bg-blue-600',
            'hover:bg-blue-700'
        );
    }
    else
    {
        submitBtn.disabled = true;

        submitBtn.classList.add(
            'bg-gray-400',
            'cursor-not-allowed'
        );

        submitBtn.classList.remove(
            'bg-blue-600',
            'hover:bg-blue-700'
        );
    }
}


// ========================
// LISTENER
// ========================

document
.querySelectorAll(
    'input, select'
)
.forEach(el => {

    el.addEventListener(
        'input',
        validateForm
    );

    el.addEventListener(
        'change',
        validateForm
    );

});

validateForm();

</script>
@endsection