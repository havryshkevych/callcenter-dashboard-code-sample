const authProvider = {
    login: ({username, password}) => {
        var english = /^[A-Za-z0-9]*$/;
        if (!english.test(username) || !english.test(password)) {
            return Promise.reject('English letters only');
        }

        const request = new Request(process.env.REACT_APP_API_ENTRYPOINT, {
            method: 'GET',
            headers: new Headers({
                'Content-Type': 'application/ld+json',
                'Authorization': 'Basic ' + btoa(username + ':' + password)
            }),
        });
        return fetch(request)
            .then(response => {
                if (response.status < 200 || response.status >= 300) {
                    throw new Error(response.statusText);
                }
                localStorage.setItem('token', btoa(username + ':' + password));
            })
    },
    logout: () => {
        localStorage.removeItem('token');
        localStorage.removeItem('permissions');
        return Promise.resolve();
    },
    checkError: () => {
        // ...
    },
    checkAuth: () => {
        return localStorage.getItem('token') ? Promise.resolve() : Promise.reject();
    },
    getPermissions: () => {
        const role = localStorage.getItem('permissions');
        return role ? Promise.resolve(role) : Promise.reject();
    }
};

export default authProvider;
