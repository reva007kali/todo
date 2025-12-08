await fetch('/push-subscribe', {
    method: 'POST',
    credentials: 'include', // PENTING
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
    },
    body: JSON.stringify(subscription)
});
