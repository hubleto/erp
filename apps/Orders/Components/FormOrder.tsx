import React, { Component, createRef, useRef, ChangeEvent } from 'react';
import { getUrlParam } from '@hubleto/react-ui/core/Helper';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import TableOrderProducts from '@hubleto/apps/Orders/Components/TableOrderProducts';
import TableDocuments from '@hubleto/apps/Documents/Components/TableDocuments';
import request from "@hubleto/react-ui/core/Request";
import TableHistories from './TableHistories';
import WorkflowSelector from '../../Workflow/Components/WorkflowSelector';
import FormInput from '@hubleto/react-ui/core/FormInput';
import OrderFormActivity, { OrderFormActivityProps, OrderFormActivityState } from './OrderFormActivity';
import ModalForm from '@hubleto/react-ui/core/ModalForm';
import Calendar from '../../Calendar/Components/Calendar';
import moment, { Moment } from "moment";

export interface FormOrderProps extends HubletoFormProps {
}

export interface FormOrderState extends HubletoFormState {
  newEntryId: number,
  showIdActivity: number,
  activityTime: string,
  activityDate: string,
  activitySubject: string,
  activityAllDay: boolean,
}

export default class FormOrder<P, S> extends HubletoForm<FormOrderProps,FormOrderState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'Hubleto/App/Community/Orders/Models/Order',
  };

  props: FormOrderProps;
  state: FormOrderState;

  refLogActivityInput: any;

  translationContext: string = 'Hubleto\\App\\Community\\Orders\\Loader';
  translationContextInner: string = 'Components\\FormOrder';

  parentApp: string = 'Hubleto/App/Community/Orders';

  constructor(props: FormOrderProps) {
    super(props);

    this.refLogActivityInput = React.createRef();

    this.state = {
      ...this.getStateFromProps(props),
      newEntryId: -1,
      showIdActivity: 0,
      activityTime: '',
      activityDate: '',
      activitySubject: '',
      activityAllDay: false,
    };
  }

  getStateFromProps(props: FormOrderProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Order')}</b> },
        { uid: 'products', title: this.translate('Products'), showCountFor: 'PRODUCTS' },
        { uid: 'history', icon: 'fas fa-clock-rotate-left', position: 'right' },
        ...this.getCustomTabs()
      ]
    };
  }

  getRecordFormUrl(): string {
    return 'orders/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  contentClassName(): string
  {
    return this.state.record.is_closed ? 'opacity-85 bg-slate-100' : '';
  }

  renderTitle(): JSX.Element {
    return <>
      <small>Order</small>
      <h2>{this.state.record.identifier ?? '-'}</h2>
    </>;
  }

  getSumPrice(recordProducts: any) {
    var sumPrice = 0;
    recordProducts.map((product, index) => {
      if (product.unit_price && product.amount && product._toBeDeleted_ != true) {
        var sum = product.unit_price * product.amount;
        if (product.vat) sum = sum + (sum * (product.vat / 100));
        if (product.discount) sum = sum - (sum * (product.discount / 100));
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

  getHeaderButtons()
  {
    return [
      ...super.getHeaderButtons(),
      {
        title: 'Generate PDF',
        onClick: () => {
          request.post(
            'orders/api/generate-pdf',
            {idOrder: this.state.record.id},
            {},
            (result: any) => {
              console.log(result);
              if (result.idDocument) {
                window.open(globalThis.main.config.projectUrl + '/documents/' + result.idDocument);
              }
            }
          );
        }
      },
      {
        title: 'Close order',
        onClick: () => { }
      }
    ]
  }

  renderTopMenu(): JSX.Element {
    const R = this.state.record;
    return <>
      {super.renderTopMenu()}
      {this.state.id <= 0 ? null : <>
        <div className='flex-2 pl-4'><WorkflowSelector parentForm={this}></WorkflowSelector></div>
        {this.inputWrapper('is_closed', {wrapperCssClass: 'flex gap-2'})}
      </>}
    </>
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':

        //@ts-ignore
        const tmpCalendar = <Calendar
          onCreateCallback={() => this.loadRecord()}
          readonly={R.is_archived}
          initialView='dayGridMonth'
          headerToolbar={{ start: 'title', center: '', end: 'prev,today,next' }}
          eventsEndpoint={globalThis.main.config.projectUrl + '/calendar/api/get-calendar-events?source=orders&idOrder=' + R.id}
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

        const recentActivitiesAndCalendar = <div className='card card-body shadow-blue-200'>
          <div className='mb-2'>
            {tmpCalendar}
          </div>
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
            return <button key={index} className={"btn btn-small btn-transparent btn-list-item " + (item.completed ? "bg-green-50" : "bg-red-50")}
              onClick={() => this.setState({showIdActivity: item.id} as FormOrderState)}
            >
              <span className="icon">{item.date_start} {item.time_start}<br/>@{item['_LOOKUP[id_owner]']}</span>
              <span className="text">
                {item.subject}
                {item.completed ? null : <div className="text-red-800">{this.translate('Not completed yet')}</div>}
              </span>
            </button>;
          })}</div> : null}
        </div>;

        return <>
          <div className='card'>
            <div className='card-body flex flex-row gap-2'>
              <div className='grow'>
                <FormInput title={"Deal"}>
                  {R.DEALS ? R.DEALS.map((item, key) => {
                    return (item.DEAL ? <a
                      key={key}
                      className='badge'
                      href={globalThis.main.config.projectUrl + '/deals/' + item.DEAL.id}
                      target='_blank'
                    >{item.DEAL.identifier}</a> : '#');
                  }) : null}
                </FormInput>
                {this.inputWrapper('identifier')}
                {this.inputWrapper('identifier_customer')}
                {this.inputWrapper('title')}
                {this.inputWrapper('id_customer')}
                {<div className='flex flex-row *:w-1/2'>
                    {this.inputWrapper('price_excl_vat')}
                    {this.inputWrapper('price_incl_vat')}
                    {this.inputWrapper('id_currency')}
                </div>}
                {this.inputWrapper('note')}
                {this.inputWrapper('id_owner')}
                {this.inputWrapper('id_manager')}
                {this.inputWrapper('date_order')}
                {this.inputWrapper('required_delivery_date')}
                {this.inputWrapper('shared_folder')}
                {this.inputWrapper('shipping_info')}
                {this.inputWrapper('id_template')}
              </div>
              <div className='border-l border-gray-200'></div>
              <div className='grow'>
                {this.state.id > 0 ? recentActivitiesAndCalendar : null}
              </div>
            </div>
          </div>
        </>;
      break;

      case 'products':
        return <TableOrderProducts
          tag={"table_order_product"}
          parentForm={this}
          uid={this.props.uid + "_table_order_product"}
          idOrder={R.id}
          // junctionTitle='Order'
          // junctionModel='Hubleto/App/Community/Orders/Models/OrderProduct'
          // junctionSourceColumn='id_order'
          // junctionSourceRecordId={R.id}
          // junctionDestinationColumn='id_product'
          readonly={R.is_archived == true ? false : !this.state.isInlineEditing}
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
          readonly={R.is_archived == true ? false : !this.state.isInlineEditing}
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
          uid='activity_form'
          isOpen={true}
          type='right'
        >
          <OrderFormActivity
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
            showInModal={true}
            showInModalSimple={true}
            onClose={() => { this.setState({ showIdActivity: 0 } as FormOrderState) }}
            onSaveCallback={(form: OrderFormActivity<OrderFormActivityProps, OrderFormActivityState>, saveResponse: any) => {
              if (saveResponse.status == "success") {
                this.setState({ showIdActivity: 0 } as FormOrderState);
              }
            }}
          ></OrderFormActivity>
        </ModalForm>
      }
    </>;
  }

}