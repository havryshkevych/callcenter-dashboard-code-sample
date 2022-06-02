import React from "react";
import Card from "../../../components/Card/Card";
import CardHeader from "../../../components/Card/CardHeader";
import CardBody from "../../../components/Card/CardBody";
import Table from '@material-ui/core/Table';
import TableBody from '@material-ui/core/TableBody';
import TableCell from '@material-ui/core/TableCell';
import TableHead from '@material-ui/core/TableHead';
import TableRow from '@material-ui/core/TableRow';
import TableFooter from '@material-ui/core/TableFooter';
import TablePagination from '@material-ui/core/TablePagination';
import {makeStyles} from "@material-ui/core/styles";

import dataProvider from "../../../dataProvider";
import moment from 'moment';

const styles = {
    cardCategoryWhite: {
        color: "rgba(255,255,255,.62)",
        margin: "0",
        fontSize: "14px",
        marginTop: "0",
        marginBottom: "0",
    },
    cardTitleWhite: {
        color: "#FFFFFF",
        marginTop: "0px",
        minHeight: "auto",
        fontWeight: "300",
        fontFamily: "'Roboto', 'Helvetica', 'Arial', sans-serif",
        marginBottom: "3px",
        textDecoration: "none",
    },
};
const useStyles = makeStyles(styles);

const Knowledge = (props) => {
    const classes = useStyles();
    const [knowledge, setKnowledge] = React.useState([]);
    const [page, setPage] = React.useState(0);
    const [pageCount, setPageCount] = React.useState(0);
    const [rowsPerPage, setRowsPerPage] = React.useState(10);
    const handleChangeRowsPerPage = (event) => {
        setRowsPerPage(parseInt(event.target.value, 10));
        setPage(1);
    };

    React.useEffect(async () => {
        let active = true;
        let paramsData = {
            user: props.user.id,
            page: page + 1,
            itemsPerPage: rowsPerPage,
        };

        await dataProvider({
            headers: {
                'Accept': 'application/ld+json',
                'Content-Type': 'application/ld+json'
            }
        }).get('/knowledge-scorings', {
            params: paramsData
        }).then(res => {
            if (active) {
                setPageCount(res.data["hydra:totalItems"]);
                setKnowledge(res.data["hydra:member"]);
            }
        })
        return () => {
            return active = false;
        };
    }, [props.user, rowsPerPage, page]);
    return <>
        <h1>Оценка знаний</h1>
        <Card>
            <CardHeader color="primary">

            </CardHeader>
            <CardBody>
                <Table className={classes.table} aria-label="simple table">
                    <TableHead>
                        <TableRow>
                            <TableCell>Дата</TableCell>
                            <TableCell>Название</TableCell>
                            <TableCell>Док.</TableCell>
                            <TableCell>Результат</TableCell>
                            <TableCell>Коефициент</TableCell>
                        </TableRow>
                    </TableHead>
                    <TableBody>
                        {knowledge.map((row, index) => (
                            <TableRow key={index}>
                                <TableCell>{moment(row.date, '').format('DD-MM-YY HH:mm')}</TableCell>
                                <TableCell>{row.name}</TableCell>
                                <TableCell>
                                    <a href={row.screenshot} target={"_blank"}>
                                        <img src={row.screenshot} alt={row.name} style={{maxWidth:"200px", maxHeight: "150px"}}/>
                                    </a>
                                </TableCell>
                                <TableCell>{row.result}</TableCell>
                                <TableCell>{row.coefficient}</TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                    <TableFooter>
                        <TableRow>
                            <TablePagination
                                rowsPerPageOptions={[10, 25, 50]}
                                colSpan={3}
                                count={pageCount}
                                rowsPerPage={rowsPerPage}
                                page={page}
                                onChangePage={(event, newPage) => {setPage(newPage)} }
                                onChangeRowsPerPage={handleChangeRowsPerPage}
                            />
                        </TableRow>
                    </TableFooter>
                </Table>
            </CardBody>
        </Card>

    </>;
}

export default Knowledge;

