export const bookmarkStore = {
  namespaced: true,
  state: () => ({
    bookmarkData: {},
  }),
  mutations: {
    updateBookmark(state, bookmarkItem) {
      state.bookmarkData = bookmarkItem;
    },
  },
  actions: {
    updateBookmark({ commit }, bookmarkItem) {
      commit('updateBookmark', bookmarkItem);
    },
  },
  getters: {
    getBookmark: (state) => state.bookmarkData,
  },
};

export const getBookmarkData = (store) => store.getters['bookmark/getBookmark'];

export const setBookmarkData = (store, bookmarkItem) => {
  store.dispatch('bookmark/updateBookmark', bookmarkItem);
};
