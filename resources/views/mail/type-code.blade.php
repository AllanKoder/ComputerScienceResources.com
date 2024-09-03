<x-app-layout>
    <form action="{{ route('email.change.verify') }}" method="POST">
        @csrf
        <label for="code">Enter the 6-digit code sent to your new email:</label>
        <input type="text" name="code" id="code" required>
        <button type="submit">Verify</button>
    </form>
</x-app-layout>