const dateFormatPlugin = {
    install(app) {
        app.config.globalProperties.$dateFormatFr = function (date) {
            const [year, month, day] = date.split('-');
            return `${day}/${month}/${year}`;
        };
        app.config.globalProperties.$dateFormatEn = function (date) {
            const [day, month, year] = date.split('/');
            return `${year}-${month}-${day}`;
        };
    },
};

export default dateFormatPlugin;
