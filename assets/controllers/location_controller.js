import {Controller} from '@hotwired/stimulus';


export default class extends Controller {

    static targets = ['output', 'btn']

    connect() {
    }

    async getCurrentLocation(event) {

        this.btnTarget.innerText = 'loading...'
        event.preventDefault()

        try {
            const position = await new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition(resolve, reject)
            })
            this.outputTarget.innerText = `lat: ${position.coords.latitude} long: ${position.coords.longitude}`
        } catch (error) {
            this.outputTarget.innerText = error.message
        } finally {
            this.btnTarget.innerText = 'current location'
        }
    }


}
