export const newsStore = {
  namespaced: true,
  state: () => ({
    news: {},
  }),
  mutations: {
    updateNews(state, newsItem) {
      state.news = newsItem;
    },
  },
  actions: {
    updateNews({ commit }, newsItem) {
      commit('updateNews', newsItem);
    },
  },
  getters: {
    getNews: (state) => state.news,
  },
};

export const getNewsData = (store) => store.getters['news/getNews'];

export const setNewsData = (store, newsItem) => {
  store.dispatch('news/updateNews', newsItem);
};
