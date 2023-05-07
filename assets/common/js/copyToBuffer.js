function copyToBuffer(data, messageBox = null, success = "Данные скопированы", error = "Не удалось скопировать данные") {
    if (navigator.clipboard) {
        // поддержка имеется, включить соответствующую функцию проекта.
        if (data) {
            navigator.clipboard.writeText(data)
            .then(() => {
                if (messageBox) {
                    new Message(messageBox, success, 'success');
                }
            })
            .catch(err => {
                console.error(error, err);
                if (messageBox) {
                    new Message(messageBox, error, 'error');
                }
            })
        }
    } else {
        // поддержки нет. Придётся пользоваться execCommand или не включать эту функцию.
        const input = document.createElement('input');
        input.setAttribute('value', data);
        document.body.appendChild(input);
        input.select();
        if (document.execCommand('copy')) {
            if (messageBox) {
                new Message(messageBox, success, 'success');
            }
        } else {
            if (messageBox) {
                new Message(messageBox, error, 'error');
            }
        }
        document.body.removeChild(input);
    }
}