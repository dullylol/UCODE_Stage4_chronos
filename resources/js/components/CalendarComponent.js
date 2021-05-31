import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import { useState } from 'react';
import Calendar from 'react-calendar';
import 'react-calendar/dist/Calendar.css';

export default class CalendarComponent extends Component {
    render() {
        const [value, onChange] = useState(new Date());
        return (
            <div>
            <Calendar
              onChange={onChange}
              value={value}
            />
          </div>
        )
    }
}


if (document.getElementById('calendar')) {
    ReactDOM.render(<Calendar />, document.getElementById('calendar'));
}
