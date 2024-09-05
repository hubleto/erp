import React, { Component, useState } from "react";
import { Badge, Calendar, Popover, Whisper } from "rsuite";
import FormActivity from "../Customers/FormActivity";
import "rsuite/Calendar/styles/index.css";

export default class CalendarComponent extends Component {
  constructor(props) {
    super(props);

    this.state = {
      events: [],
      loading: true,
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
            <a
              target="_blank"
              href={`customers/activities?recordId=${eventForDate.id}`}
            >
              <strong>{eventForDate.title}</strong>
            </a>
          </div>
        </div>
      );
    }
    return null;
  };

  render() {
    const { loading } = this.state;

    if (loading) {
      return <div>Loading Calendar...</div>;
    }

    const html =
    <div>
      <Calendar bordered renderCell={this.renderCell} />;
      <FormActivity/>
    </div>

    return html;

  }
}
