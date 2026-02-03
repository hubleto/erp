import React, { Component, createRef, useRef, ChangeEvent } from 'react';
import { getUrlParam } from '@hubleto/react-ui/core/Helper';
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import TableItems from './TableItems';
import TableDocuments from '@hubleto/apps/Documents/Components/TableDocuments';
import TablePayments from './TablePayments';
import request from "@hubleto/react-ui/core/Request";
import TableHistories from './TableHistories';
import FormInput from '@hubleto/react-ui/core/FormInput';
import OrdersFormActivity, { OrdersFormActivityProps, OrdersFormActivityState } from './OrdersFormActivity';
import ModalForm from '@hubleto/react-ui/core/ModalForm';
import Calendar from '../../Calendar/Components/Calendar';
import moment, { Moment } from "moment";
import Lookup from '@hubleto/react-ui/core/Inputs/Lookup';
import HtmlFrame from "@hubleto/react-ui/core/HtmlFrame";

export interface FormOrderProps extends FormExtendedProps {
}

export interface FormOrderState extends FormExtendedState {
  showIdActivity: number,
  activityTime: string,
  activityDate: string,
  activitySubject: string,
  activityAllDay: boolean,
  selectParentDeal?: boolean,
}

export default class FormOrder<P, S> extends FormExtended<FormOrderProps,FormOrderState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    icon: 'fas fa-money-check-dollar',
    model: 'Hubleto/App/Community/Orders/Models/Order',
    renderWorkflowUi: true,
  };

  props: FormOrderProps;
  state: FormOrderState;

  refLogActivityInput: any;
  refActivityModal: any;
  refActivityForm: any;
  refPreview: any;

  translationContext: string = 'Hubleto\\App\\Community\\Orders\\Loader';
  translationContextInner: string = 'Components\\FormOrder';

  parentApp: string = 'Hubleto/App/Community/Orders';

  constructor(props: FormOrderProps) {
    super(props);

    this.refLogActivityInput = React.createRef();
    this.refActivityModal = React.createRef();
    this.refActivityForm = React.createRef();
    this.refPreview = React.createRef();

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

  getStateFromProps(props: FormOrderProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Order')}</b> },
        { uid: 'items', title: this.translate('Items'), showCountFor: 'ITEMS' },
        { uid: 'preview', title: this.translate('Preview') },
        { uid: 'calendar', title: this.translate('Calendar') },
        { uid: 'payments', title: this.translate('Payments') },
        { uid: 'history', icon: 'fas fa-clock-rotate-left', position: 'right' },
        { uid: 'timeline', icon: 'fas fa-timeline', position: 'right' },
        ...this.getCustomTabs()
      ]
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
    const tabUid = this.state.activeTabUid;
    switch (tabUid) {
      case 'preview':
        this.updatePreview(this.state.record.id_template);
      break;
    }
  }

  updatePreview(idTemplate: number) {
    request.post(
      'orders/api/get-preview-html',
      {
        idOrder: this.state.record.id,
        idTemplate: idTemplate,
      },
      {},
      (result: any) => {
        this.refPreview.current.setState({content: result.html});
      }
    );
  }

  showPreviewVars() {
    request.post(
      'orders/api/get-preview-vars',
      {
        idOrder: this.state.record.id,
        idTemplate: this.state.record.id_template,
      },
      {},
      (vars: any) => {
        this.refPreview.current.setState({content: '<pre>' + JSON.stringify(vars.vars, null, 2) + '</pre>'});
      }
    );
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.state.record.purchase_sales == 1 ? 'Purchase Order' : 'Sales Order'}</small>
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
                    {this.divider(null)}
                    {this.inputWrapper('shared_folder')}
                  </div>
                  <div className='grow'>
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
                    {this.inputWrapper('id_owner', {wrapperCssClass: 'flex gap-2'})}
                    {this.inputWrapper('id_manager', {wrapperCssClass: 'flex gap-2'})}
                    {this.divider(null)}
                    {this.inputWrapper('shipping_info')}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </>;
      break;

      case 'preview':
        return <div className='flex gap-2 h-full'>
          <div className='flex-1 w-72 flex flex-col gap-2'>
            <div className='grow'>
              {this.inputWrapper('id_template', {
                uiStyle: 'buttons-vertical',
                onChange: (input: any) => {
                  this.updatePreview(input.state.value);
                }
              })}
            </div>
            <div className='grow'>
              <button
                className='btn btn-transparent btn-large mb-4'
                onClick={() => {
                  request.post(
                    'orders/api/generate-pdf',
                    {idOrder: this.state.record.id},
                    {},
                    (result: any) => {
                      this.reload();
                    }
                  );
                }}
              >
                <span className='icon'><i className='fas fa-print'></i></span>
                <span className='text'>{this.translate('Export to PDF')}</span>
              </button>
              {this.inputWrapper('pdf', {readonly: true})}
            </div>
          </div>
          <div className="flex-3 gap-2 h-full card card-body">
            <HtmlFrame
              ref={this.refPreview}
              className='w-full h-full'
            />
            <a
              href='#'
              onClick={() => {
                this.showPreviewVars();
              }}
            >{this.translate('Show variables which can be used in template')}</a>
          </div>
        </div>;
      break;

      case 'calendar':
        //@ts-ignore
        const tmpCalendarSmall = <Calendar
          onCreateCallback={() => this.loadRecord()}
          initialView='dayGridMonth'
          headerToolbar={{ start: 'title', center: '', end: 'prev,today,next' }}
          eventsEndpoint={globalThis.hubleto.config.projectUrl + '/calendar/api/get-calendar-events?source=orders&idOrder=' + R.id}
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
          eventsEndpoint={globalThis.hubleto.config.projectUrl + '/calendar/api/get-calendar-events?source=orders&idOrder=' + R.id}
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
        return <TableItems
          key={"table_order_item"}
          tag={"table_order_item"}
          parentForm={this}
          uid={this.props.uid + "_table_order_item"}
          idOrder={R.id}
          // junctionTitle='Order'
          // junctionModel='Hubleto/App/Community/Orders/Models/Item'
          // junctionSourceColumn='id_order'
          // junctionSourceRecordId={R.id}
          // junctionDestinationColumn='id_item'
          // readonly={!this.state.isInlineEditing}
        />;

      break;

      case 'payments':
        return <TablePayments
          key={"table_order_payment"}
          tag={"table_order_payment"}
          parentForm={this}
          uid={this.props.uid + "_table_order_payment"}
          idOrder={R.id}
        />;

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

      case 'history':
        return <>
          <TableHistories
            uid={this.props.uid + "_table_order_history"}
            data={{ data: R.HISTORY }}
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
        super.renderTab(tabUid);
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