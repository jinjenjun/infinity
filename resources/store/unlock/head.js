export const headStore = {
  namespaced: true,
  state: () => ({
    title: '論壇文章',
  }),
  mutations: {
    updateTitle(state, title) {
      state.title = title;
    },
  },
  actions: {
    updateTitle({ commit }, title) {
      commit('updateTitle', title);
    },
  },
  getters: {
    getTitle: (state) => state.title,
  },
};

export const getHeadTitle = (store) => store.getters['head/getTitle'];

export const setHeadTitle = (store, title) => {
  store.dispatch('head/updateTitle', title);
};
