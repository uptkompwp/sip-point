import { defineStore } from "pinia";

export const useMenuStore = defineStore("menu-state", {
    state: () => ({
        showMenu: false,
        isMobile: false,
    }),
    getters: {
        getShowMenu: (state) => state.showMenu,
        getIsMobile: (state) => state.isMobile,
    },
    actions: {
        setShowMenu(payload) {
            this.showMenu = payload;
        },
        setIsMobile(payload) {
            this.isMobile = payload;
        },
    },
});
