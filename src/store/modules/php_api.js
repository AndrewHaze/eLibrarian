const state = {
  link: "http://l.mgr.loc",
  possibility: true,
  reg: false,
  at: 'eLibrarian v.0.0.1',
  ai: -1,
  si: -1,
  bll: "cover",
  ip: "on",
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
  //вид списка книг
  blLook: state => state.bll,
  //Инфо панель книги
  iPanel: state => state.ip,
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
  setblLook(state, value) {
    state.bll = value
  },
  setInfoPanel(state, value) {
    state.ip = value
  },
}

export default {
  state,
  getters,
  mutations,
}
