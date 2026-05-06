export const headMenuStore = {
  namespaced: true,
  state: () => ({
    menu: {},
  }),
  mutations: {
    updateMenu(state, menuItem) {
      state.menu = menuItem;
    },
  },
  actions: {
    updateMenu({ commit }, menuItem) {
      commit('updateMenu', menuItem);
    },
  },
  getters: {
    getMenu: (state) => state.menu,
  },
};

export const getMenu = (store) => store.getters['headMenu/getMenu'];

export const setMenu = (store, menuItem) => {
  store.dispatch('headMenu/updateMenu', menuItem);
};
