class Calendario {
    calendar;
    
    constructor() {
        var calendarEl = document.getElementById('calendar');
        var d = new Date();

        this.calendar = new FullCalendar.Calendar(calendarEl, {
            plugins: ['dayGrid', 'timeGrid', 'list', 'interaction'],
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'timeGridWeek,listWeek'
            },
            locale: 'pt-br',
            defaultDate: d,
            defaultView: 'timeGridWeek',
            navLinks: true,
            selectable: true,
            resources: [
                { id: 'a', title: 'Sala JK' }
            ],
            select: function(dateOrObj) {
                if (logado === '1') {
                    var data = {
                        'data': dateOrObj.startStr,
                    };
                    $.post(urlSalvar, data,
                        function(data, status){
                            this.addEventSource([{
                                id: data.message.id,
                                title: data.message.nome,
                                start: data.message.data,
                            }])
                        }.bind(this)
                    );
                }
            },
            eventClick: function(info) {
                if (logado === '1') {
                    var data = {
                        'id': info.event.id,
                    };
                    $.post(urlApagar, data,
                        function(data, status){
                            if (data.mensagem !== '' ) {
                                info.event.remove();
                            }
                        }
                    );
                }
            },
        });

        this.calendar.render();        
    };
    
    addEvento(id,nome,data) {
        this.calendar.addEventSource([{
            id: id,
            title: nome,
            start: data,
        }]);
    };
    
    ini() {
        var parser = new DOMParser;
        var dom = parser.parseFromString(eventos,'text/html');
        eventos = dom.body.textContent;        
        eventos = jQuery.parseJSON(eventos);
        
        for (var [key, value] of Object.entries(eventos)) {
            this.addEvento(value.id,value.nome,value.data);
        }
    };
};

calendario = new Calendario;
calendario.ini();