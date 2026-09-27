// 検索バーの入力候補をドロップダウンで表示する（素のJavaScript）
const input = document.getElementById('search-input');
const list = document.getElementById('search-suggest-list');

if (input && list) {
    const suggestUrl = input.dataset.suggestUrl;
    const showUrlTemplate = input.dataset.showUrl;
    let debounceTimer = null;
    // IME（日本語入力）で変換中かどうかのフラグ
    let isComposing = false;

    // ドロップダウンを閉じて中身を空にする
    const closeList = () => {
        list.classList.add('hidden');
        list.innerHTML = '';
    };

    // 候補配列からドロップダウンを組み立てる
    const renderList = (animeTitles) => {
        list.innerHTML = '';

        if (animeTitles.length === 0) {
            closeList();
            return;
        }

        animeTitles.forEach((animeTitle) => {
            const li = document.createElement('li');
            const a = document.createElement('a');
            // __ID__ を実際のIDに差し替えて詳細画面URLを作る
            a.href = showUrlTemplate.replace('__ID__', animeTitle.id);
            a.className = 'block px-3 py-2 text-sm text-gray-800 hover:bg-gray-100';
            // XSS対策のため、タイトルはinnerHTMLではなくtextContentで挿入する
            a.textContent = animeTitle.title;
            li.appendChild(a);
            list.appendChild(li);
        });

        list.classList.remove('hidden');
    };

    // 入力値をもとに、デバウンスしてから候補を取得する
    const scheduleSuggest = () => {
        const q = input.value.trim();

        clearTimeout(debounceTimer);

        // 空文字なら通信せずに候補を閉じる
        if (q === '') {
            closeList();
            return;
        }

        // 入力が止まってから通信する（連打時の無駄な通信と、古い応答による表示の上書きを防ぐ）
        debounceTimer = setTimeout(() => {
            fetch(`${suggestUrl}?q=${encodeURIComponent(q)}`)
                .then((response) => response.json())
                .then(renderList)
                .catch(closeList);
        }, 250);
    };

    // 変換開始時はフラグを立てて、変換中の候補取得を止める
    input.addEventListener('compositionstart', () => {
        isComposing = true;
    });

    // 変換確定時にフラグを戻し、確定後の文字列で1回だけ候補取得する
    input.addEventListener('compositionend', () => {
        isComposing = false;
        scheduleSuggest();
    });

    input.addEventListener('input', () => {
        // 変換中の中間文字列は候補取得の対象にしない
        if (isComposing) {
            return;
        }

        scheduleSuggest();
    });

    // 入力欄・候補の外をクリックしたら候補を閉じる
    document.addEventListener('click', (event) => {
        if (!input.contains(event.target) && !list.contains(event.target)) {
            closeList();
        }
    });
}
