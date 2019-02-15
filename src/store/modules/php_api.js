const state = {
  link: "http://l.mgr.loc",
  possibility: true,
  reg: false,
  at: 'eLibrarian v.0.0.1',
  ai: -1,
  si: -1,
  gi: -1,
  bi: -1,
  gt: '',
  gty: '',
  oc: 0,
  tab: 0,
  bll: "cover",
  ip: ['on'],
  reader: false,
}

const getters = {
  //домен для подстановки в пути к файлам
  prefix: state => state.link,
  //флаг: при отсутвии юзеров на экране рег. не показываем ссылку на вход
  loginForm: state => state.possibility,
  //флаг: окно после регистрации 
  congratulation: state => state.reg,
  appTitle: state => state.at,
  authorID: state => state.ai,
  seriesID: state => state.si,
  genresID: state => state.gi,
  bkID: state => state.bi,
  //для жанров
  genresType: state => state.gty,
  genresTitle: state => state.gt,
  orderCode: state => state.oc,

  /******************* Интерфейс *******************/
  currentTab: state => state.tab, //текущая вкладка
  blLook: state => state.bll, //вид списка книг
  iPanel: state => state.ip, //Инфо панель книги
  /*************************************************/

  //reader
  isReader: state => state.reader,
}

function storageAvailable(type) {
  try {
      var storage = window[type],
          x = '__storage_test__';
      storage.setItem(x, x);
      storage.removeItem(x);
      return true;
  }
  catch(e) {
      return e instanceof DOMException && (
          // everything except Firefox
          e.code === 22 ||
          // Firefox
          e.code === 1014 ||
          // test name field too, because code might not be present
          // everything except Firefox
          e.name === 'QuotaExceededError' ||
          // Firefox
          e.name === 'NS_ERROR_DOM_QUOTA_REACHED') &&
          // acknowledge QuotaExceededError only if there's something already stored
          storage.length !== 0;
  }
}

const mutations = {
  checkUsers(state, available) {
    state.possibility = available
  },
  showRegModal(state, show) {
    state.reg = show
  },
  setAuthorID(state, value) {
    state.ai = value
  },
  setSeriesID(state, value) {
    state.si = value
  },
  setGenresID(state, value) {
    state.gi = value
  },
  setGenresType(state, value) {
    state.gty = value
  },
  setGenresTitle(state, value) {
    state.gt = value
  },
  setBookID(state, value) {
    state.bi = value
  },
  setOrderCode(state, value) {
    state.oc = value
  },
  setCurrentTab(state, value) {
    state.tab = value
    if (storageAvailable('localStorage')) {
      localStorage.setItem('currentTab', value);
    }
  },
  setblLook(state, value) {
    state.bll = value
    if (storageAvailable('localStorage')) {
      localStorage.setItem('lookOfBookList', value);
    }
  },
  setInfoPanel(state, value) {
    state.ip = value
    if (storageAvailable('localStorage')) {
      localStorage.setItem('InfoPanel', value);
    }
  },
  setReader(state, value) {
    state.reader = value
  },
}

export default {
  state,
  getters,
  mutations,
}
