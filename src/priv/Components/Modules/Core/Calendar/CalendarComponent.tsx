import React, { Component, useState } from "react";
import { Badge, Calendar, Popover, Whisper } from "rsuite";
import FormActivity from "../Customers/FormActivity";
import "rsuite/Calendar/styles/index.css";
import ModalSimple from "adios/ModalSimple";

export default class CalendarComponent extends Component {
  constructor(props) {
    super(props);

    this.state = {
      events: [],
      loading: true,
      showIdActivity: 0
    };
  }

  componentDidMount() {
    this.fetchCalendarEvents();
  }

  fetchCalendarEvents = () => {
    fetch("customers/activities/get")
      .then((response) => response.json())
      .then((data) => {
        this.setState({ events: data, loading: false });
      })
      .catch((error) => {
        console.error("Error fetching calendar events:", error);
        this.setState({ loading: false });
      });
  };

  renderCell = (date) => {
    const { events } = this.state;

    const eventForDate = events.find(
      (event) => new Date(event.date).toDateString() === date.toDateString()
    );

    if (eventForDate) {
      return (
        <div>
          <div style={{ backgroundColor: "lightblue", padding: "5px" }}>
            <button onClick={() => {this.setState({showIdActivity: eventForDate.id})}}>
              <strong>{eventForDate.title}</strong>
            </button>
          </div>
        </div>
      );
    }
    return null;
  };

  render(): JSX.Element {
    const { loading } = this.state;

    if (loading) {
      return <div>Loading Calendar...</div>;
    }

    const html =
    <div>
      <Calendar bordered renderCell={this.renderCell} />;
      {this.state.showIdActivity <= 0 ? null :
        <ModalSimple
          uid='waste_diagram_modal_form_technology'
          isOpen={true}
          type='right'
        >
          <FormActivity
            id={this.state.showIdActivity}
            showInModal={true}
            showInModalSimple={true}
            onClose={() => { this.setState({showIdActivity: 0}); }}
          ></FormActivity>
        </ModalSimple>
      }
    </div>

    return html;

  }
}
