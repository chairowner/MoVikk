class Message {
    constructor(messageBox, message, type, duration = 1.5, maxOpacity = .9, animationOn = true) {
        const transitionTime = 25;
        let bottomAnimation = 0, rightAnimation = 0;
        if (animationOn) bottomAnimation = rightAnimation = -20;
        const units = 'px';
        
        // стили уведомления
        let style = {
            display: 'none',
            height: 'auto',
            padding: '10px',
            cursor: 'default',
            display: 'flex',
            justifyContent: 'center',
            alignItems: 'center',
            opacity: 0,
            zIndex: 1000,
            transition: 'opacity .' + (transitionTime - 6) + 's, display .' + (transitionTime - 10) + 's, margin .' + transitionTime + 's ease-in-out'
        };
        
        if (animationOn) style.marginBottom = bottomAnimation + units;
        
        // создание объекта
        this.alert = $('<div class="alert ' + type + ' user-select-none">' + message + '</div>');
        // добавление стилей
        this.alert.css(style);
            
        // добавление на страницу
        messageBox.append(this.alert);

        // обработка нажатия
        this.alert.on('click', function() {
            this.remove();
        });
            
        // вывод на экран
        setTimeout(() => {
            
            this.alert.css({display: 'flex', marginBottom: 0, opacity: maxOpacity});
            
            setTimeout(() => {

                let opacityNew = 0;
                this.alert.css({marginRight: rightAnimation + units, opacity: opacityNew});

                // удаление элемента со страницы
                setTimeout(() => {
                    this.alert.css({display: 'none'});
                    this.alert.remove();
                }, 2000); 

            }, duration * 1000);

        }, 0);
    }

    getTypes(string = false) {
        if (string) return 'primary, secondary, success, danger, warning, info, light, dark';
        else return ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'];
    } 
}