<h2>Ubah Password</h2>

@if(session('error'))
<p style="color:red">{{ session('error') }}</p>
@endif

@if(session('success'))
<p style="color:green">{{ session('success') }}</p>
@endif

<form action="/password" method="POST">
    @csrf

    <label>Password Lama:</label>
    <input type="password" name="old_password">

    <br><br>

    <label>Password Baru:</label>
    <input type="password" name="new_password">

    <br><br>

    <label>Konfirmasi Password Baru:</label>
    <input type="password" name="new_password_confirmation">

    <br><br>

    <button type="submit">Update Password</button>
</form>