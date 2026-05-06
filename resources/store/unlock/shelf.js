export const shelfStore = {
  namespaced: true,
  state: () => ({
    shelfData: {},
  }),
  mutations: {
    updateShelf(state, shelfItem) {
      state.shelfData = shelfItem;
    },
  },
  actions: {
    updateShelf({ commit }, shelfItem) {
      commit('updateShelf', shelfItem);
    },
  },
  getters: {
    getShelf: (state) => state.shelfData,
  },
};

export const getShelfData = (store) => store.getters['shelf/getShelf'];

export const setShelfData = (store, shelfItem) => {
  store.dispatch('shelf/updateShelf', shelfItem);
};
