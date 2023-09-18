import { defineStore } from "pinia";

export const useGlobalStore = defineStore("global-state", {
    state: () => ({
        loading: false,
    }),
    getters: {
        getLoading: (state) => state.loading,
    },
    actions: {
        setLoading(payload) {
            this.loading = payload;
        },
    },
});
