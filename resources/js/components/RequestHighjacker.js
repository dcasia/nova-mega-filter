const novaRequest = Nova.request

const interceptors = []
const interceptorsInstance = []

Nova.request = (...params) => {

    for (const param of params) {

        for (const interceptor of interceptors) {
            interceptor(param)
        }

    }

    const axiosInstance = novaRequest(...params)

    if (axiosInstance instanceof Promise) {
        return axiosInstance
    }

    for (const interceptor of interceptors) {

        interceptorsInstance.push({
            instance: axiosInstance,
            interceptor: axiosInstance.interceptors.request.use(config => interceptor(config)),
        })

    }

    return axiosInstance

}

function cleanUpInterceptors() {

    for (const { instance, interceptor } of interceptorsInstance) {
        instance.interceptors.request.eject(interceptor)
    }

    while (interceptors.length) {
        interceptors.pop()
    }

}

export { interceptors, cleanUpInterceptors }
