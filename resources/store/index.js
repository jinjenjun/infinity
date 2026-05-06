import { createStore } from 'vuex';
import * as unlock from './unlock';

const persistedModules = [
  { module: 'head', key: 'aitehubHeadData', mutationTypes: ['head/updateTitle'] },
  { module: 'headMenu', key: 'aitehubHeadMenu', mutationTypes: ['headMenu/updateMenu'] },
  { module: 'member', key: 'aitehubMember', mutationTypes: ['member/updateLogin'] },
  { module: 'music', key: 'aitehubMusic', mutationTypes: ['music/updateMusic'] },
  { module: 'information', key: 'aitehubInformation', mutationTypes: ['information/updateTitle'] },
  { module: 'bookmark', key: 'aitehubBookmark', mutationTypes: ['bookmark/updateBookmark'] },
  { module: 'shelf', key: 'aitehubShelf', mutationTypes: ['shelf/updateShelf'] },
  { module: 'news', key: 'aitehubNews', mutationTypes: ['news/updateNews'] },
  { module: 'category', key: 'aitehubCategory', mutationTypes: ['category/updateCategory'] },
];

const buildPersistedMutationMap = () => {
  const mutationMap = new Map();

  persistedModules.forEach(({ module, key, mutationTypes }) => {
    mutationTypes.forEach((mutationType) => {
      mutationMap.set(mutationType, { module, key });
    });
  });

  return mutationMap;
};

const isEmptyObject = (value) =>
  value &&
  typeof value === 'object' &&
  !Array.isArray(value) &&
  Object.keys(value).length === 0;

const persistStore = (store) => {
  if (typeof window === 'undefined' || !window.localStorage) {
    return;
  }

  const restoredState = { ...store.state };

  persistedModules.forEach(({ module, key }) => {
    const storedValue = window.localStorage.getItem(key);

    if (!storedValue) {
      return;
    }

    try {
      const parsedValue = JSON.parse(storedValue);

      if (parsedValue && typeof parsedValue === 'object' && !Array.isArray(parsedValue)) {
        restoredState[module] = {
          ...restoredState[module],
          ...parsedValue,
        };
      } else {
        window.localStorage.removeItem(key);
      }
    } catch (error) {
      window.localStorage.removeItem(key);
    }
  });

  store.replaceState(restoredState);

  const mutationMap = buildPersistedMutationMap();

  store.subscribe((mutation, state) => {
    const config = mutationMap.get(mutation.type);

    if (!config) {
      return;
    }

    const nextState = state[config.module];

    if (!nextState || isEmptyObject(nextState)) {
      window.localStorage.removeItem(config.key);
      return;
    }

    window.localStorage.setItem(config.key, JSON.stringify(nextState));
  });
};

export const store = createStore({
  modules: {
    head: unlock.head.headStore,
    headMenu: unlock.headMenu.headMenuStore,
    member: unlock.member.memberStore,
    music: unlock.music.musicStore,
    information: unlock.information.informationStore,
    bookmark: unlock.bookmark.bookmarkStore,
    shelf: unlock.shelf.shelfStore,
    news: unlock.news.newsStore,
    category: unlock.category.categoryStore,
  },
  plugins: [persistStore],
});

export const vuexData = {
  unlock,
};
