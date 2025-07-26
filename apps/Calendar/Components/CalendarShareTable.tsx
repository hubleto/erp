import React from "react";
import request from "@hubleto/react-ui/core/Request";
import Table from "@hubleto/react-ui/core/Table";
import TranslatedComponent from "@hubleto/react-ui/core/TranslatedComponent";

interface CalendarShareTableProps {
  configs?: any,
}

interface CalendarShareTableState {
  configs: any,
  loading: boolean,
  newCalendar: string,
}

export default class CalendarShareTable extends TranslatedComponent<CalendarShareTableProps, CalendarShareTableState> {

  props: CalendarShareTableProps;
  state: CalendarShareTableState;

  constructor(props) {
    super(props);

    this.state = {
      configs: props.configs,
      loading: false,
      newCalendar: "",
    };
  }

  componentDidMount() {
  }

  shareCalendar(calendar: any) {
    this.setState({
      loading: true,
      newCalendar: calendar,
      configs: this.state.configs,
    }, () =>
    {
      this.setState({
        loading: false,
        newCalendar: calendar,
        configs: this.state.configs,
      });
    });
  }

  refresh() {
    this.setState({
      loading: true,
      newCalendar: "",
      configs: this.state.configs,
    }, () => {

      request.get(
        'calendar/api/get-shared-calendars',
        {}, (data: any) => {
          let backup = this.state.configs;
          Object.entries(backup).forEach(backupObject => {
            backup[backupObject[0]].shared = 0;
          })
          data.forEach(calendar => {
            if (calendar.calendar in backup) {
              backup[calendar.calendar].shared += 1;
            }
          });
          this.setState({
            loading: false,
            newCalendar: "",
            configs: backup,
          })
        });
    });
  }

  stopSharingConfirm(calendar: any) {
    globalThis.main.showDialogConfirm(
      this.translate(
        'You are about to remove all shared access to the calendar "' + calendar + '". Do you want to continue?',
        'HubletoApp\\Community\\Calendar\\Components\\CalendarShareTable'
      ),
      {
        headerClassName: 'dialog-danger-header',
        contentClassName: 'dialog-danger-content',
        header: this.translate('Stop sharing "' + calendar + '"', 'HubletoApp\\Community\\Calendar\\Components\\CalendarShareTable'),
        yesText: this.translate('Yes, remove all shares', 'HubletoApp\\Community\\Calendar\\Components\\CalendarShareTable'),
        yesButtonClass: 'btn-danger',
        onYes: () => { this.stopSharingCalendar(calendar); },
        noText: this.translate('No, keep current sharing', 'HubletoApp\\Community\\Calendar\\Components\\CalendarShareTable'),
        onNo: () => { },
      }
    );

  }

  stopSharingCalendar(calendar: any) {
    this.setState({
      loading: true,
      configs: this.state.configs,
    },
      () => { request.post(
        'calendar/api/stop-sharing-calendar',
        {
          calendar: calendar,
        }, {}, (data: any) => { this.refresh(); }, (data: any) => { this.refresh(); });
        }
      );
  }

  removeShare(table: any) {
    let recordToDelete: any = null;

    for (let i in table.state.data?.data) {
      if (table.state.data?.data[i]._toBeDeleted_) {
        recordToDelete = table.state.data?.data[i];
        break;
      }
    }

    request.post(
      'calendar/api/stop-sharing-calendar',
      {
        calendar: recordToDelete?.calendar ?? "",
        share_key: recordToDelete?.share_key ?? "",
      }, {}, (data: any) => { this.refresh(); });
  }

  renderCalendars(calendars: any) {
    return calendars.map((calendarObject: any[]) => (
      <tr key={calendarObject[0]} style={{"borderLeft": "1em solid " + calendarObject[1].color}}>
        <td>{calendarObject[1].title}</td>
        <td className="text-right">
          { calendarObject[1].shared == 1 && "Shared 1 time"}
          { calendarObject[1].shared > 1 && "Shared " + calendarObject[1].shared + " times"}
          { calendarObject[1].shared > 0 &&
            <button onClick={() => this.stopSharingConfirm(calendarObject[0])} className="btn btn-transparent btn-small">
              <span className="icon"><i className="fas fa-chain-broken"></i></span>
              <span className="text">Stop sharing</span>
            </button>
          }
          <button onClick={() => this.shareCalendar(calendarObject[0])} className="btn btn-transparent btn-small">
            <span className="icon"><i className="fas fa-share-nodes"></i></span>
            <span className="text">Share calendar { calendarObject[1].shared >= 1 && "again"}</span>
          </button>
        </td>
      </tr>
    ));
  }

  render(): JSX.Element {
    let calendars = this.renderCalendars(Object.entries(this.state.configs));

    return <>
      <div className="card w-1/2 m-auto">
        <div className="card-header">
          Share calendar as ICS
        </div>
        <div className="card-body">
          <table className="table-default dense w-full">
            <tbody>
            {calendars}
            </tbody>
          </table>
        </div>
      </div>
      <div className="card w-1/2 m-auto mt-2">
        <div className="card-header">
          List of all created shares
        </div>
        <div className="card-body">
          { this.state.loading ?
            <div role="status">
              <svg aria-hidden="true" className="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
                   viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                  fill="currentColor"/>
                <path
                  d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                  fill="currentFill"/>
              </svg>
              <span className="sr-only">Loading...</span>
            </div>
            :
            <Table
              model="HubletoApp/Community/Calendar/Models/SharedCalendar"
              formReactComponent="SharedCalendarForm"
              description={{
                ui: {
                  showHeader: false,
                }
              }}
              recordId={this.state.newCalendar != "" ? -1 : 0}
              onDeleteRecord={(table) => this.removeShare(table)}
              formProps={{
                description: {
                  defaultValues: {
                    calendar: this.state.newCalendar
                  }
                }
              }}
              formCustomProps={{
                onUpdate: () => this.refresh(),
              }}
            >
            </Table>
          }
        </div>
      </div>
    </>
      ;
  }
}
