
export const categoryStore = {
  namespaced: true,
  state: () => ({
    category: {},
  }),
  mutations: {
    updateCategory(state, categoryItem) {
      state.category = categoryItem;
    },
  },
  actions: {
    updateCategory({ commit }, categoryItem) {
      commit('updateCategory', categoryItem);
    },
  },
  getters: {
    getCategory: (state) => state.category,
  },
};

export const getCategoryData = (store) => store.getters['category/getCategory'];

export const setCategoryData = (store, categoryItem) => {
  store.dispatch('category/updateCategory', categoryItem);
};

