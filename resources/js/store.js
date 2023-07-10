import { createStore } from 'vuex';

export default createStore({
    state: {
        isDarkMode: JSON.parse(localStorage.getItem('isDarkMode')) || false
    },
    mutations: {
        toggleDarkMode(state) {
            state.isDarkMode = !state.isDarkMode;
            localStorage.setItem('isDarkMode', JSON.stringify(state.isDarkMode));
        },
        setDarkMode(state, isDarkMode) {
            state.isDarkMode = isDarkMode;
        }
    },

});
