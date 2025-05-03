async function startMining(userId) {
    const prevHash = await fetchLatestHash();
    const data = `Reward for user ${userId}`;
    const difficulty = '0000';
    const start = Date.now();
    let nonce = 0;
    let hash = '';
    let text = '';

    console.log("Starting mining...");
    console.log("prevHash:", prevHash);
    console.log("data:", data);

    while (true) {
        text = prevHash + data + nonce;
        hash = CryptoJS.SHA256(text).toString();

        if (hash.startsWith(difficulty)) break;

        nonce++;
        if (Date.now() - start > 10000) {
            alert("⏳ Mining timed out. Try again.");
            return;
        }
    }

    console.log("✅ Valid block mined!");
    console.log("Sending to server:");
    console.log({ prevHash, data, nonce, hash });

    const response = await fetch('mine_submit.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ prevHash, data, nonce, hash })
    });

    const result = await response.text();
    alert(result);
}

async function fetchLatestHash() {
    const res = await fetch('get_latest_hash.php');
    const text = await res.text();
    console.log("Fetched Latest Hash:", text);
    return text.trim();
}
