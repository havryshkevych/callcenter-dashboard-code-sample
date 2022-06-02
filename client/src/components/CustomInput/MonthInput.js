import React from 'react';
import MomentUtils from '@date-io/moment';
import { MuiPickersUtilsProvider, DatePicker } from '@material-ui/pickers';
import { createMuiTheme } from "@material-ui/core";
import { ThemeProvider } from "@material-ui/styles";
import "moment/locale/ru";
import moment from "moment";

moment.locale("ru");
const materialTheme = createMuiTheme({
    overrides: {
        MuiInputBase: {
          input: {
              color: "#FFF !important"
          }
        },
        MuiPickersToolbar: {
            toolbar: {
                background: 'linear-gradient(60deg, #3C4858, #777)',
                boxShadow: '0 4px 20px 0 rgb(0 0 0 / 14%), 0 7px 10px -5px rgb(51 51 51 / 40%)'
            },
        },
        MuiPickersCalendarHeader: {
            switchHeader: {
                color: "white",
            },
        },
    },
});

const MonthInput = props => {
    const [selectedDate, handleDateChange] = React.useState(props.initialValue ? new Date(props.initialValue) : new Date());
    const handleChange = data => {
        handleDateChange(data);
        props.setDate({
            startOfMonth: data.startOf('month').format('YYYY-MM-DD HH:mm:ss'),
            endOfMonth: data.endOf('month').format('YYYY-MM-DD HH:mm:ss')
        });
    }

    return <>
        <ThemeProvider theme={materialTheme}>
            <MuiPickersUtilsProvider utils={MomentUtils}>
                <DatePicker
                    views={["year", "month"]}
                    minDate={moment(new Date("2021-01-01")).startOf('year')}
                    maxDate={moment().endOf('month')}
                    value={selectedDate}
                    onChange={handleChange}
                />
            </MuiPickersUtilsProvider>
        </ThemeProvider>
    </>;
}

export default MonthInput;