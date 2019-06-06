const apiCall = ({
  url,
  data,
  method,
  ...args
}) => new Promise((resolve, reject) => {
  setTimeout(() => {
    try {
      if (method === 'POST') {
        const curentUserName = data.username;
        resolve({
          token: Math.random()  // Generate random number, eg: 0.123456
            .toString(36)       // Convert  to base-36 : "0.4fzyo82mvyr"
            .slice(-8),         // Cut off last 8 characters : "yo82mvyr",
          login: curentUserName
        })

      } else {
        const login = sessionStorage.getItem('user-login') || 'unkown';
        resolve({
          name: login
        })
      }
    } catch (err) {
      reject(new Error(err))
    }
  }, 1000)
})

export default apiCall
