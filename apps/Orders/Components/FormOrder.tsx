import React, { Component, createRef, useRef, ChangeEvent } from 'react';
import { getUrlParam } from '@hubleto/react-ui/core/Helper';
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import TableItems from './TableItems';
import TableQuotes from './TableQuotes';
import TableActivities from '@hubleto/apps/Worksheets/Components/TableActivities';
import TableDocuments from '@hubleto/apps/Documents/Components/TableDocuments';
import request from "@hubleto/react-ui/core/Request";
import TableHistories from './TableHistories';
import FormInput from '@hubleto/react-ui/core/FormInput';
import OrdersFormActivity, { OrdersFormActivityProps, OrdersFormActivityState } from './OrdersFormActivity';
import ModalForm from '@hubleto/react-ui/core/ModalForm';
import Calendar from '../../Calendar/Components/Calendar';
import moment, { Moment } from "moment";
import Lookup from '@hubleto/react-ui/core/Inputs/Lookup';
import { ProgressBar } from 'primereact/progressbar';

export interface FormOrderProps extends FormExtendedProps {
}

export interface FormOrderState extends FormExtendedState {
  statistics?: any,
  salaries?: any,
  showIdActivity: number,
  activityTime: string,
  activityDate: string,
  activitySubject: string,
  activityAllDay: boolean,
  selectParentDeal?: boolean,
}

export default class FormOrder<P, S> extends FormExtended<FormOrderProps, FormOrderState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    icon: 'fas fa-money-check-dollar',
    model: 'Hubleto/App/Community/Orders/Models/Order',
    renderWorkflowUi: true,
    renderOwnerManagerUi: true,
    renderPreviewUi: true,
  };

  props: FormOrderProps;
  state: FormOrderState;

  refLogActivityInput: any = React.createRef();
  refActivityModal: any = React.createRef();
  refActivityForm: any = React.createRef();
  refTableItemsInvoicing: any = React.createRef();
  refTableActivities: any = React.createRef();

  translationContext: string = 'Hubleto\\App\\Community\\Orders\\Loader';
  translationContextInner: string = 'Components\\FormOrder';

  parentApp: string = 'Hubleto/App/Community/Orders';

  constructor(props: FormOrderProps) {
    super(props);

    this.state = {
      ...this.getStateFromProps(props),
      showIdActivity: 0,
      activityTime: '',
      activityDate: '',
      activitySubject: '',
      activityAllDay: false,
      selectParentDeal: false,
    };
  }

  getTabsLeft() {
    return [
      { uid: 'default', title: <b>{this.translate('Order')}</b> },
      { uid: 'items', title: this.translate('Items') },
      { uid: 'calendar', title: this.translate('Calendar') },
      { uid: 'quotes', title: this.translate('Quotes') },
      // { uid: 'payments', title: this.translate('Payments') },
      { uid: 'worksheet', title: this.translate('Worksheet') },
      { uid: 'invoicing', title: this.translate('Invoicing') },
      { uid: 'statistics', title: this.translate('Statistics') },
      ...super.getTabsLeft(),
    ];
  }

  getTabsRight() {
    return [
      { uid: 'history', icon: 'fas fa-clock-rotate-left', position: 'right' },
      { uid: 'timeline', icon: 'fas fa-timeline', position: 'right' },
      ...super.getTabsRight(),
    ]
  }

  getStateFromProps(props: FormOrderProps) {
    return {
      ...super.getStateFromProps(props),
      salaries: {},
    };
  }

  getRecordFormUrl(): string {
    return 'orders/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  contentClassName(): string
  {
    return this.state.record.is_closed ? 'bg-slate-100' : '';
  }

  onTabChange() {
    super.onTabChange();

    const tabUid = this.state.activeTabUid;
    switch (tabUid) {
      case 'statistics':
        request.post(
          'orders/api/get-statistics',
          { idOrder: this.state.record.id },
          {},
          (data: any) => {
            this.setState({statistics: data});
          }
        )
      break;
    }
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.state.record.purchase_sales == 1 ? this.translate('Purchase Order') : this.translate('Sales Order')}</small>
      <h2>{this.state.record.identifier ?? '-'}</h2>
    </>;
  }

  getSumPrice(recordItems: any) {
    var sumPrice = 0;
    recordItems.map((item, index) => {
      if (item.unit_price && item.amount && item._toBeDeleted_ != true) {
        var sum = item.unit_price * item.amount;
        if (item.vat) sum = sum + (sum * (item.vat / 100));
        if (item.discount) sum = sum - (sum * (item.discount / 100));
        sumPrice += sum;
      }
    });
    return Number(sumPrice.toFixed(2));
  }

  logCompletedActivity() {
    request.get(
      'orders/api/log-activity',
      {
        idOrder: this.state.record.id,
        activity: this.refLogActivityInput.current.value,
      },
      (result: any) => {
        this.loadRecord();
        this.refLogActivityInput.current.value = '';
      }
    );
  }

  scheduleActivity() {
    this.setState({
      showIdActivity: -1,
      activityDate: moment().add(1, 'week').format('YYYY-MM-DD'),
      activityTime: moment().add(1, 'week').format('H:00:00'),
      activitySubject: this.refLogActivityInput.current.value,
      activityAllDay: false,
    } as FormOrderState);
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        let nextActivity = null;
        let nextActivityDate = null;

        if (R.ACTIVITIES) {
          Object.keys(R.ACTIVITIES).map((key) => {
            if (nextActivityDate !== null) return;
            const activity = R.ACTIVITIES[key];
            const dateStart = moment(activity.date_start);
            if (dateStart.isAfter()) {
              nextActivity = activity;
              nextActivityDate = dateStart;
            }
          });
        }

        return <>
          {this.input('purchase_sales', { uiStyle: 'buttons' })}
          <div className='card mt-2'>
            <div className='card-body flex flex-row gap-2'>
              <div className='grow'>
                <div className='flex gap-2'>
                  <div className='grow'>
                    {R.purchase_sales == 1 ?
                      this.inputWrapper('id_supplier')
                    : this.inputWrapper('id_customer')}
                  </div>
                  <div>
                    <FormInput title={"Deal"}>
                      {this.state.selectParentDeal ? <>
                        <Lookup
                          model='Hubleto/App/Community/Deals/Models/Deal'
                          cssClass='font-bold'
                          onChange={(input: any, value: any) => {
                            request.post(
                              'orders/api/set-parent-deal',
                              {
                                idOrder: this.state.record.id,
                                idDeal: value,
                              },
                              {},
                              (data: any) => {
                                this.setState({selectParentDeal: false}, () => this.reload());
                              }
                            )
                          }}
                        ></Lookup>
                      </> : <>
                        {R.DEALS ? R.DEALS.map((item, key) => {
                          if (!item.DEAL) return null;
                          return (item.DEAL ? <a
                            key={key}
                            className='badge'
                            href={globalThis.hubleto.config.projectUrl + '/deals/' + item.DEAL.id}
                            target='_blank'
                          >#{item.DEAL.identifier}&nbsp;{item.DEAL.title}</a> : '#');
                        }) : null}
                        <button
                          className='btn btn-small btn-transparent'
                          onClick={() => {
                            this.setState({selectParentDeal: true});
                          }}
                        >
                          <span className='text'>{this.translate('Select parent deal')}</span>
                        </button>
                      </>}
                    </FormInput>
                  </div>
                </div>
                <div className='flex gap-2'>
                  <div className='grow'>
                    {this.inputWrapper('identifier', {cssClass: 'text-2xl'})}
                    {this.inputWrapper('title', {cssClass: 'text-2xl'})}
                    {this.divider(null)}
                    <div className='flex gap-2'>
                      <div>
                        {this.inputWrapper('price_excl_vat')}
                        {this.inputWrapper('price_incl_vat')}
                      </div>
                      <div>
                        {this.inputWrapper('id_currency', {wrapperCssClass: 'flex gap-2', uiStyle: 'select'})}
                        {this.inputWrapper('payment_period', {wrapperCssClass: 'flex gap-2'})}
                      </div>
                    </div>
                    {this.divider(null)}
                    {this.inputWrapper('date_order')}
                    {this.inputWrapper('required_delivery_date')}
                    {this.inputWrapper('date_expiration')}
                    {this.inputWrapper('date_next_invoice_expected')}
                    {this.divider(null)}
                    {this.inputWrapper('shared_folder')}
                  </div>
                  <div className='grow'>
                    {this.state.id > 0 ? <>
                      {nextActivityDate ?
                        <div className='block alert alert-success'>
                          <i className='fas fa-calendar mr-2'></i>
                          Next activity is planned for <b>{nextActivityDate.format('YYYY-MM-DD')}</b>.<br/>
                          <br/>
                          <i>{nextActivity.subject}</i>
                        </div>
                      : <div className='block alert alert-danger'>
                          <i className='fas fa-calendar mr-2'></i>
                          No future activity is planned.
                        </div>
                      }
                    </> : null}
                    {this.inputWrapper('identifier_external', {wrapperCssClass: 'flex gap-2'})}
                    <div className='flex gap-2 items-center w-full'>
                      <div className='grow'>
                        {this.inputWrapper('prepaid_working_hours', {wrapperCssClass: 'flex gap-2'})}
                      </div>
                      <div className='grow'>
                        {this.input('prepaid_working_hours_period')}
                      </div>
                    </div>
                    {this.inputWrapper('note', {cssClass: 'bg-yellow-50 border-none'})}
                    {this.divider(null)}
                    {this.inputWrapper('shipping_info')}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </>;
      break;

      case 'calendar':
        //@ts-ignore
        const tmpCalendarSmall = <Calendar
          onCreateCallback={() => this.loadRecord()}
          initialView='dayGridMonth'
          headerToolbar={{ start: 'title', center: '', end: 'prev,today,next' }}
          eventsEndpoint={globalThis.hubleto.config.projectUrl + '/calendar/api/get-calendar-events?calendar=orders&idOrder=' + R.id}
          onDateClick={(date, time, info) => {
            this.setState({
              activityDate: date,
              activityTime: time,
              activityAllDay: false,
              showIdActivity: -1,
            } as FormOrderState);
          }}
          onEventClick={(info) => {
            this.setState({
              showIdActivity: parseInt(info.event.id),
            } as FormOrderState);
            info.jsEvent.preventDefault();
          }}
        ></Calendar>;

        const recentActivitiesAndCalendar = <div className='card card-body flex flex-col gap-2'>
          <div>
            {tmpCalendarSmall}
          </div>
          <div>
            <div className="hubleto component input"><div className="input-element w-full flex gap-2">
              <input
                className="w-full bg-blue-50 border border-blue-800 p-1 text-blue-800 placeholder-blue-300"
                placeholder={this.translate('Type recent activity here')}
                ref={this.refLogActivityInput}
                onKeyUp={(event: any) => {
                  if (event.keyCode == 13) {
                    if (event.shiftKey) {
                      this.scheduleActivity();
                    } else {
                      this.logCompletedActivity();
                    }
                  }
                }}
                onChange={(event: ChangeEvent<HTMLInputElement>) => {
                  this.refLogActivityInput.current.value = event.target.value;
                }}
              />
            </div></div>
            <div className='mt-2'>
              <button onClick={() => {this.logCompletedActivity()}} className="btn btn-blue-outline btn-small w-full">
                <span className="icon"><i className="fas fa-check"></i></span>
                <span className="text">{this.translate('Log completed activity')}</span>
                <span className="shortcut">{this.translate('Enter')}</span>
              </button>
              <button onClick={() => {this.scheduleActivity()}} className="btn btn-small w-full btn-blue-outline">
                <span className="icon"><i className="fas fa-clock"></i></span>
                <span className="text">{this.translate('Schedule activity')}</span>
                <span className="shortcut">{this.translate('Shift+Enter')}</span>
              </button>
            </div>
            {this.divider(this.translate('Most recent activities'))}
            {R.ACTIVITIES ? <div className="list">{R.ACTIVITIES.reverse().slice(0, 7).map((item, index) => {
              return <>
                <button key={index} className={"btn btn-small btn-transparent btn-list-item " + (item.completed ? "bg-green-50" : "bg-red-50")}
                  onClick={() => this.setState({showIdActivity: item.id} as FormOrderState)}
                >
                  <span className="icon">{item.date_start} {item.time_start}<br/>@{item['_LOOKUP[id_owner]']}</span>
                  <span className="text">
                    {item.subject}
                    {item.completed ? null : <div className="text-red-800">{this.translate('Not completed yet')}</div>}
                  </span>
                </button>
              </>
            })}</div> : null}
          </div>
        </div>;

        //@ts-ignore
        const tmpCalendarLarge = <Calendar
          onCreateCallback={() => this.loadRecord()}
          initialView='timeGridWeek'
          views={"timeGridDay,timeGridWeek,dayGridMonth,listYear"}
          eventsEndpoint={globalThis.hubleto.config.projectUrl + '/calendar/api/get-calendar-events?calendar=orders&idOrder=' + R.id}
          onDateClick={(date, time, info) => {
            this.setState({
              activityDate: date,
              activityTime: time,
              activityAllDay: false,
              showIdActivity: -1,
            } as FormOrderState);
          }}
          onEventClick={(info) => {
            this.setState({
              showIdActivity: parseInt(info.event.id),
            } as FormOrderState);
            info.jsEvent.preventDefault();
          }}
        ></Calendar>;

        return <>
          <div className='flex gap-2 mt-2'>
            <div className='flex-2 w-2/3'>
              {tmpCalendarLarge}
            </div>
            <div className='flex-1 w-1/3'>
              {this.state.id > 0 ? recentActivitiesAndCalendar : null}
            </div>
          </div>
          {/* {this.state.showIdActivity == 0 ? null :
            <ModalForm
              ref={this.refActivityModal}
              form={this.refActivityForm}
              uid='activity_form'
              isOpen={true}
              type='right'
            >
              <OrdersFormActivity
                ref={this.refActivityForm}
                modal={this.refActivityModal}
                id={this.state.showIdActivity}
                isInlineEditing={true}
                description={{
                  defaultValues: {
                    id_order: R.id,
                    id_contact: R.id_contact,
                    date_start: this.state.activityDate,
                    time_start: this.state.activityTime == "00:00:00" ? null : this.state.activityTime,
                    date_end: this.state.activityDate,
                    all_day: this.state.activityAllDay,
                    subject: this.state.activitySubject,
                  }
                }}
                idCustomer={R.id_customer}
                onClose={() => { this.setState({ showIdActivity: 0 } as FormOrderState) }}
                onSaveCallback={(form: OrdersFormActivity<OrdersFormActivityProps, OrdersFormActivityState>, saveResponse: any) => {
                  if (saveResponse.status == "success") {
                    this.setState({ showIdActivity: 0 } as FormOrderState);
                  }
                }}
              ></OrdersFormActivity>
            </ModalForm>
          } */}
        </>;
      break;

      case 'items':
        return <div className='flex flex-col gap-2'>
          <div>
            {this.inputWrapper('description_before')}
          </div>
          <TableItems
            key={"table_order_item"}
            tag={"table_order_item"}
            parentForm={this}
            view={"orderOverview"}
            uid={this.props.uid + "_table_order_item"}
            idOrder={R.id}
          />
          <div>
            {this.inputWrapper('description_after')}
          </div>
        </div>;
      break;

      case 'quotes':
        return <TableQuotes
          key={"table_order_quote"}
          tag={"table_order_quote"}
          parentForm={this}
          uid={this.props.uid + "_table_order_quote"}
          idOrder={R.id}
        />;
      break;

      case 'invoicing':
        return <div className='flex flex-col gap-2'>
          <TableItems
            key={"table_order_items_invoicing"}
            tag={"table_order_items_invoicing"}
            ref={this.refTableItemsInvoicing}
            parentForm={this}
            uid={this.props.uid + "_table_order_items_invoicing"}
            idOrder={R.id}
            view="invoicing"
          />
          <div>
            <button
              className='btn btn-primary btn-large'
              onClick={() => {
                const selection = this.refTableItemsInvoicing.current.state.selection;
                const idItems = selection.map((item) => item.id);
                request.post(
                  'orders/api/prepare-items-for-invoice',
                  {
                    idOrder: this.state.record.id,
                    idItems: idItems,
                  },
                  {},
                  (result: any) => {
                    this.refTableItemsInvoicing.current.reload();
                  }
                );
              }}
            >
              <span className='icon'><i className='fas fa-file-invoice'></i></span>
              <span className='text'>{this.translate('Prepare selected for invoice')}</span>
            </button>
          </div>
        </div>;

      break;

      case 'statistics':
        if (this.state.statistics) {
          let totalWorkedHours = 0;
          let totalChargeableHours = 0;
          let totalCostsByWorker = 0;

          if (!this.state.statistics || !this.state.statistics.projects) return null;

          return <div>{Object.keys(this.state.statistics.projects).map((idProject) => {
            const P = this.state.statistics.projects[idProject];
            return <div className='card'>
              <div className='card-header'>{P.project.identifier} {P.project.title}</div>
              <div className='card-body flex gap-2'>
                <div className='card'>
                  <div className='card-header'>{this.translate('Worked hours & costs by month')}</div>
                  <div className='card-body'>
                    <table className='table-default dense'>
                      <tbody>
                        {P.workedByMonth.map((item, key) => {
                          totalWorkedHours += parseFloat(item.worked_hours);
                          return <tr key={key}>
                            <td>{item.year}-{item.month}</td>
                            <td>{item.worked_hours} {this.translate('hours')}</td>
                          </tr>;
                        })}
                      </tbody>
                      <tfoot>
                        <tr>
                          <td className='bg-primary text-white p-2'>{this.translate('Total')}</td>
                          <td className='bg-primary text-white p-2'>{globalThis.hubleto.numberFormat(totalWorkedHours)} {this.translate('hours')}</td>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>

                <div className='card'>
                  <div className='card-header'>{this.translate('Chargeable hours by month')}</div>
                  <div className='card-body'>
                    <table className='table-default dense'>
                      <tbody>
                        {P.chargeableByMonth.map((item, key) => {
                          totalChargeableHours += parseFloat(item.worked_hours);
                          return <tr key={key}>
                            <td>{item.year}-{item.month}</td>
                            <td>{item.worked_hours} {this.translate('hours')}</td>
                          </tr>;
                        })}
                      </tbody>
                      <tfoot>
                        <tr>
                          <td className='bg-primary text-white p-2'>{this.translate('Total')}</td>
                          <td className='bg-primary text-white p-2'>{globalThis.hubleto.numberFormat(totalChargeableHours)} {this.translate('hours')}</td>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
                <div className='card'>
                  <div className='card-header'>{this.translate('Labor costs calculator')}</div>
                  <div className='card-body'>
                    <table className='table-default dense'>
                      <thead>
                        <tr>
                          <th>{this.translate('User')}</th>
                          <th>{this.translate('Worked hours')}</th>
                          <th>{this.translate('Salary')}</th>
                          <th>{this.translate('Labor costs')}</th>
                        </tr>
                      </thead>
                      <tbody>
                        {P.workedByUser.map((item, key) => {
                          let workerCosts = item.worked_hours * this.state.salaries[item.id_worker];
                          totalCostsByWorker += workerCosts;
                          return <tr key={key}>
                            <td>{item.worker_name}</td>
                            <td>{item.worked_hours} {this.translate('hours')}</td>
                            <td><div className="flex gap-2 items-center">
                              <input
                                value={this.state.salaries[item.id_worker] ?? ''}
                                className="w-12 bg-white"
                                onChange={(e) => {
                                  let salaries = this.state.salaries;
                                  salaries[item.id_worker] = e.currentTarget.value;
                                  this.setState({salaries: salaries});
                                }}
                              /> €/h
                            </div></td>
                            <td>
                              {globalThis.hubleto.currencyFormat(workerCosts)}
                            </td>
                          </tr>;
                        })}
                      </tbody>
                      <tfoot>
                        <tr>
                          <td className='bg-primary text-white p-2'>{this.translate('Total')}</td>
                          <td className='bg-primary text-white p-2'>&nbsp;</td>
                          <td className='bg-primary text-white p-2'>&nbsp;</td>
                          <td className='bg-primary text-white p-2'>{globalThis.hubleto.currencyFormat(totalCostsByWorker)}</td>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
            </div>;
          })}</div>;
        } else {
          return <ProgressBar mode="indeterminate" style={{ height: '8px' }}></ProgressBar>;
        }
      break;

      case 'documents':
        return <TableDocuments
          key={"table_order_document"}
          parentForm={this}
          uid={this.props.uid + "_table_order_document"}
          junctionTitle='Order'
          junctionModel='Hubleto/App/Community/Orders/Models/OrderDocument'
          junctionSourceColumn='id_order'
          junctionSourceRecordId={R.id}
          junctionDestinationColumn='id_document'
          readonly={!this.state.isInlineEditing}
        />;

      break;

      case 'worksheet':
        let latestItemDue = moment('2000-01-01');

        if (R.ITEMS) {
          Object.keys(R.ITEMS).map((key) => {
            const item = R.ITEMS[key];
            if (moment(item.date_due).isAfter(latestItemDue)) {
              latestItemDue = moment(item.date_due);
            }
          });
        }
        return <div>
          <button
            className='btn btn-add-outline mb-2'
            onClick={() => {
              this.refTableActivities.current.setColumnSearch(
                'date_worked', '>' + latestItemDue.format('YYYY-MM-DD')
              );
            }}
          >
            <span className='icon'><i className='fas fa-calendar'></i></span>
            <span className='text'>Show activities since the latest item due ({latestItemDue.format('YYYY-MM-DD')})</span>
          </button>
          <TableActivities
            ref={this.refTableActivities}
            key={"table_order_activities"}
            parentForm={this}
            uid={this.props.uid + "_table_order_activities"}
            idOrder={R.id}
            readonly={true}
          />
        </div>;

      break;

      case 'history':
        return <>
          <TableHistories
            uid={this.props.uid + "_table_order_history"}
            data={{ records: R.HISTORY }}
            descriptionSource='props'
            description={{
              ui: {
                showHeader: false,
                showFooter: false,
              },
              permissions: {
                canCreate: false,
                canUpdate: false,
                canDelete: false,
                canRead: true,
              },
              columns: {
                short_description: { type: "text", title: "Short Description" },
                date_time: { type: "datetime", title: "Date Time"},
              },
              inputs: {
                short_description: { type: "text", title: "Short Description" },
                date_time: { type: "datetime", title: "Date Time"},
              }
            }}
            isUsedAsInput={true}
            isInlineEditing={false}
            onRowClick={(table: TableHistories, row: any) => table.openForm(row.id)}
          />
        </>;
      break;

      case 'timeline':
        return this.renderTimeline([
          {
            data: (thisForm) => thisForm.state.record.ACTIVITIES,
            icon: 'fas fa-calendar',
            color: '#32678fff',
            timestampFormatter: (entry) => entry.date_start,
            valueFormatter: (entry) => entry.subject,
            userNameFormatter: (entry) => entry['_LOOKUP[id_owner]'],
          },
          { 
            data: (thisForm) => thisForm.state.record.WORKFLOW_HISTORY,
            icon: 'fas fa-timeline',
            color: '#8f3248ff',
            timestampFormatter: (entry) => entry.datetime_change,
            valueFormatter: (entry) => entry.WORKFLOW_STEP?.name ?? '---',
            userNameFormatter: (entry) => entry.USER?.nick,
          },
        ]);
      break;

      default:
        return super.renderTab(tabUid);
      break;
    }
  }

  renderContent(): JSX.Element {
    const R = this.state.record;
    return <>
      {super.renderContent()}
      {this.state.showIdActivity == 0 ? <></> :
        <ModalForm
          ref={this.refActivityModal}
          form={this.refActivityForm}
          uid='activity_form'
          isOpen={true}
          type='right'
        >
          <OrdersFormActivity
            id={this.state.showIdActivity}
            ref={this.refActivityForm}
            modal={this.refActivityModal}
            isInlineEditing={true}
            description={{
              defaultValues: {
                id_order: R.id,
                id_contact: R.id_contact,
                date_start: this.state.activityDate,
                time_start: this.state.activityTime == "00:00:00" ? null : this.state.activityTime,
                date_end: this.state.activityDate,
                all_day: this.state.activityAllDay,
                subject: this.state.activitySubject,
              }
            }}
            idCustomer={R.id_customer}
            onClose={() => { this.setState({ showIdActivity: 0 } as FormOrderState) }}
            onSaveCallback={(form: OrdersFormActivity<OrdersFormActivityProps, OrdersFormActivityState>, saveResponse: any) => {
              if (saveResponse.status == "success") {
                this.setState({ showIdActivity: 0 } as FormOrderState);
              }
            }}
          ></OrdersFormActivity>
        </ModalForm>
      }
    </>;
  }

}