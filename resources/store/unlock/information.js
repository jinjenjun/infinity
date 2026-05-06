export const informationStore = {
  namespaced: true,
  state: () => ({
    title: {},
  }),
  mutations: {
    updateTitle(state, footerItem) {
      state.title = footerItem;
    },
  },
  actions: {
    updateTitle({ commit }, footerItem) {
      commit('updateTitle', footerItem);
    },
  },
  getters: {
    getTitle: (state) => state.title,
  },
};

export const getTitle = (store) => store.getters['information/getTitle'];

export const setTitle = (store, footerItem) => {
  store.dispatch('information/updateTitle', footerItem);
};
