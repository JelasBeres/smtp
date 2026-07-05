<main style="max-width:640px;margin:4rem auto;font-family:sans-serif">
    <h1>Unsubscribe</h1>
    <p>Confirm that you no longer want to receive campaign emails.</p>
    <form method="post" action="{{ route('unsubscribe.store', $token) }}">@csrf<button>Confirm unsubscribe</button></form>
</main>
