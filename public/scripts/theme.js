        // function to set a given theme/color-scheme
        function setTheme(themeName) {
            localStorage.setItem('theme', themeName);
            document.documentElement.className = themeName;
        }

        // function to toggle between light and white theme
        function toggleTheme() {
            if (localStorage.getItem('theme') === 'white') {
                setTheme('root');
            } else {
                setTheme('white');
            }
        }

        // Immediately invoked function to set the theme on initial load
        (function () {
            if (localStorage.getItem('theme') === 'white') {
                setTheme('white');
            } else {
                setTheme('root');
            }
        })();