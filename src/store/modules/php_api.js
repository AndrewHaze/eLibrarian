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
  bll: "cover",
  ip: "on",
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
  //вид списка книг
  blLook: state => state.bll,
  //Инфо панель книги
  iPanel: state => state.ip,
  //reader
  isReader: state => state.reader,
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
  setblLook(state, value) {
    state.bll = value
  },
  setInfoPanel(state, value) {
    state.ip = value
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
