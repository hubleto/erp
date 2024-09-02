import React, { Component } from "react";
import { Badge, Calendar, Popover, Whisper } from "rsuite";
import "rsuite/Calendar/styles/index.css";
import request from "adios/Request";
import { exit } from "process";

export default class CalendarComponent extends Component {

  props: {
    data: any;
  }

  getData(idCompany?: number) {
    request.post(
      "customers/activities/get",
      {
        idCompany: idCompany ?? 0,
      },
      {},
      (res: any) => {
        console.log(res);
        this.props.data = res;
      },
      (err: any) => {
        alert(err);
      }
    );
  }

  renderCell(date) {
    var transformedDate = date.getFullYear() + "-" + date.getMonth() + "-" + date.getDate();

    if (
      date.getFullYear() === this.props.data[transformedDate].date.getFullYear() &&
      date.getMonth() === this.props.data[transformedDate].date.getMonth() &&
      date.getDate() === this.props.data[transformedDate].date.getDate()
    ) {
      // Customize the cell for the specific date
      return (
        <div
          style={{
            backgroundColor: "lightblue",
            borderRadius: "50%",
            padding: "5px",
          }}
        >
          {this.props.data[date]} - {this.props.data[date].title}
        </div>
      );
    }
  }

  render() {
    this.getData();
    return <Calendar isoWeek={true} bordered renderCell={this.renderCell} />;
  }
}
