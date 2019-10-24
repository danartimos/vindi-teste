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
                    alert('dan');
                }
            },
            eventClick: function(info) {
                if (logado === '1') {
                    
                    //info.event.remove();
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
    
    salvarEvento() {
        
    };
    
    removerEvento() {
        
    };
    
    ini() {
        var parser = new DOMParser;
        var dom = parser.parseFromString(eventos,'text/html');
        eventos = dom.body.textContent;        
        eventos = jQuery.parseJSON(eventos);

        eventos.forEach(function(data){
            this.addEvento(data.id,data.nome,data.data);
        }.bind(this));
    };
};

calendario = new Calendario;
calendario.ini();