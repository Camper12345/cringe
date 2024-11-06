(async function(){
    function getCookieValue(name) 
    {
        const regex = new RegExp(`(^| )${name}=([^;]+)`);
        const match = document.cookie.match(regex);
        if (match) {
            return match[2];
        }
    }

    let visit = getCookieValue('visit_id');
    
    let userDataResponse = fetch('/api/user', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            'visit_id': visit,
        })
    });

    window.userData = await (await userDataResponse).json();

    function fillAuthBar() {
        let userName = document.getElementById('authBarUserName');
        if(userName) {
            userName.innerText = window.userData.name;
            userName.onclick = async (e) => {
                let oldName = window.userData.name;
                let newName = prompt('Enter new username', oldName);
                if(!newName || newName === oldName) {
                    return;
                }

                let nameChangeResponse = await fetch('/api/user/rename', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        'name': newName,
                    })
                });

                if(nameChangeResponse.ok) {
                    window.userData.name = newName;
                    fillAuthBar();
                } else {
                    alert((await nameChangeResponse.json()).detail || 'Unknown error');
                }
            };
        }
    }

    if(document.readyState == 'interactive') {
        fillAuthBar();
    } else {
        addEventListener('loaded', (e) => {
            fillAuthBar();
        });
    }

})();