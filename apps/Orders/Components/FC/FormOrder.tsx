import React, { useState, useEffect, createRef } from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import { useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';
import request from '@hubleto/react-ui/core/Request';
import moment from "moment";
import LookupInput from '@hubleto/react-ui/components/fc/Inputs/Lookup';
import CalendarTab from '@hubleto/apps/Calendar/Components/FC/CalendarTab';
import Spinner from '@hubleto/react-ui/components/fc/Spinner';
import TableItems from './TableItems';
import TableQuotes from './TableQuotes';
import TableActivities from '@hubleto/apps/Worksheets/Components/FC/TableActivities';
import CalendarFormActivity from '@hubleto/apps/Calendar/Components/FC/CalendarFormActivity';
import Table from '@hubleto/react-ui/components/fc/Table';

export interface FormOrderProps extends FormProps {}

const componentName = 'FormOrder'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Orders';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormOrderProps) => {
  const form = React.useContext(FormMetaContext);
  const [selectParentDeal, setSelectParentDeal] = useState(false);

  const purchaseSales: number = useRecordField('purchase_sales', 0);
  const DEALS: any = useRecordField('DEALS', []);
  const ACTIVITIES: any = useRecordField('ACTIVITIES', []);

  let nextActivity = null;
  let nextActivityDate = null;

  if (ACTIVITIES) {
    Object.keys(ACTIVITIES).map((key) => {
      if (nextActivityDate !== null) return;
      const activity = ACTIVITIES[key];
      const dateStart = moment(activity.date_start);
      if (!activity.completed && dateStart.isAfter()) {
        nextActivity = activity;
        nextActivityDate = dateStart;
      }
    });
  }

  return <>
    <div className='flex-dyn'>
      <div className='flex-5'>
        <Input field='title' renderOnlyInputField customInputProps={{cssClass: 'text-[2em] border border-primary p-1 shadow rounded'}} />
      </div>
      <div className='flex-1'>
        <Input field='identifier' renderOnlyInputField customInputProps={{cssClass: 'text-[2em] border border-primary p-1 shadow rounded'}} />
      </div>
    </div>
    <div className='flex-dyn'>
      <div className='grow'>
        <Input field={purchaseSales == 1 ? 'id_supplier' : 'id_customer'} />
        <Input title={"Deal"}>
          {selectParentDeal ? <LookupInput
            model='Hubleto/App/Community/Deals/Models/Deal'
            cssClass='font-bold'
            onChange={(input: any, value: any) => {
              request.post(
                'deals/api/set-parent-deal',
                { idOrder: form.id, idDeal: value },
                {},
                (data: any) => { setSelectParentDeal(false); }
              )
            }}
          ></LookupInput>
          : <>
            {DEALS ? DEALS.map((item, key) => {
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
                setSelectParentDeal(true);
              }}
            >
              <span className='text'>{T.translate('Select parent deal')}</span>
            </button>
          </>}
        </Input>
        <div className='flex-dyn'>
          <div>
            <Input field='price_excl_vat' />
            <Input field='price_incl_vat' />
          </div>
          <div>
            <Input field='id_currency' customInputProps={{wrapperCssClass: 'flex gap-2', uiStyle: 'select'}} />
            <Input field='payment_period' customInputProps={{wrapperCssClass: 'flex gap-2'}} />
          </div>
        </div>
        <Input field='date_order' />
        <Input field='required_delivery_date' />
        <Input field='date_expiration' />
        <Input field='date_next_invoice_expected' />
        <Input field='shared_folder' />
      </div>
      <div className='grow'>
        {form.id > 0 ? <>
          {nextActivityDate ?
            <div className='block alert alert-success'>
              <i className='fas fa-calendar mr-2'></i>
              Next follow-up is planned for <b>{nextActivityDate.format('YYYY-MM-DD')}</b>.<br/>
              <br/>
              <i>{nextActivity.subject}</i>
            </div>
          : <div className='block alert alert-danger'>
              <i className='fas fa-calendar mr-2'></i>
              No follow-up in your calendar.
            </div>
          }
        </> : null}
        <Input field='identifier_external' customInputProps={{wrapperCssClass: 'flex-dyn'}} />
        <Input field='prepaid_working_hours' customInputProps={{wrapperCssClass: 'flex-dyn'}} />
        <Input field='prepaid_working_hours_period' renderOnlyInputField />
        <Input field='note' customInputProps={{cssClass: 'bg-yellow-50 border-none'}} />
        <Input field='shipping_info' />
      </div>
    </div>
  </>;
}

/** TabItems */
const TabItems = (props: FormOrderProps) => {
  const form = React.useContext(FormMetaContext);

  if (form.id <= 0) return <div className='alert alert-info'>Create order first</div>;

  return <div className='flex flex-col gap-2'>
    <div>
      <Input field='description_before' />
    </div>
    <TableItems
      tag={"table_order_item"}
      parentForm={form}
      view={"orderOverview"}
      uid={props.uid + "_table_order_item"}
      idOrder={form.id}
    />
    <div>
      <Input field='description_after' />
    </div>
  </div>;
}

/** TabCalendar */
const TabCalendar = (props: FormProps) => {
  const form = React.useContext(FormMetaContext);
  if (form.id <= 0) return <div className='alert alert-info'>Create order first</div>;
  return <CalendarTab
    loadEventsEndpoint={'calendar/api/get-calendar-events?calendar=orders&idOrder=' + form.id}
    logActivityEndpoint={'orders/api/log-activity?idOrder=' + form.id}
    renderActivityForm={(calendarTab: any) => {
      return <CalendarFormActivity
        calendarTab={calendarTab}
        customInputFields={['id_order']}
        defaultValues={{id_order: form.id}}
        model='Hubleto/App/Community/Orders/Models/OrderActivity'
      ></CalendarFormActivity>;
    }}
  ></CalendarTab>;
}

/** TabQuotes */
const TabQuotes = (props: FormOrderProps) => {
  const form = React.useContext(FormMetaContext);
  if (form.id <= 0) return <div className='alert alert-info'>Create order first</div>;
  return <TableQuotes
    tag={"table_order_quote"}
    parentForm={form}
    uid={props.uid + "_table_order_quote"}
    idOrder={form.id}
  />;
}

/** TabWorksheet */
const TabWorksheet = (props: FormOrderProps) => {
  const form = React.useContext(FormMetaContext);
  const ITEMS = useRecordField('ITEMS');
  const refTableActivities = createRef();

  if (form.id <= 0) return <div className='alert alert-info'>Create order first</div>;

  let latestItemDue = moment('2000-01-01');

  if (ITEMS) {
    Object.keys(ITEMS).map((key) => {
      const item = ITEMS[key];
      if (moment(item.date_due).isAfter(latestItemDue)) {
        latestItemDue = moment(item.date_due);
      }
    });
  }
  return <div>
    <button
      className='btn btn-add-outline mb-2'
      onClick={() => {
        refTableActivities.current.setColumnSearch(
          'date_worked', '>' + latestItemDue.format('YYYY-MM-DD')
        );
      }}
    >
      <span className='icon'><i className='fas fa-calendar'></i></span>
      <span className='text'>Show activities since the latest item due ({latestItemDue.format('YYYY-MM-DD')})</span>
    </button>
    <TableActivities
      ref={refTableActivities}
      parentForm={form}
      uid={props.uid + "_table_order_activities"}
      idOrder={form.id}
      readonly={true}
    />
  </div>;
}

/** TabInvoicing */
// const TabInvoicing = (props: FormOrderProps) => {
//   const form = React.useContext(FormMetaContext);
//   const refTableItemsInvoicing = createRef();

//   if (form.id <= 0) return <div className='alert alert-info'>Create order first</div>;

//   return <div className='flex flex-col gap-2'>
//     <TableItems
//       tag={"table_order_items_invoicing"}
//       ref={refTableItemsInvoicing}
//       parentForm={form}
//       uid={props.uid + "_table_order_items_invoicing"}
//       idOrder={form.id}
//       view="invoicing"
//     />
//     <div>
//       <button
//         className='btn btn-primary btn-large'
//         onClick={() => {
//           const selection = refTableItemsInvoicing.current.state.selection;
//           const idItems = selection.map((item) => item.id);
//           request.post(
//             'orders/api/prepare-items-for-invoice',
//             {
//               idOrder: form.id,
//               idItems: idItems,
//             },
//             {},
//             (result: any) => {
//               refTableItemsInvoicing.current.reload();
//             }
//           );
//         }}
//       >
//         <span className='icon'><i className='fas fa-file-invoice'></i></span>
//         <span className='text'>{T.translate('Prepare selected for invoice')}</span>
//       </button>
//     </div>
//   </div>;
// }

/** TabStatistics */
const TabStatistics = (props: FormOrderProps) => {
  const form = React.useContext(FormMetaContext);

  const [salaries, setSalaries] = useState({});
  const [statistics, setStatistics] = useState({});

  if (form.id <= 0) return <div className='alert alert-info'>Create order first</div>;

  useEffect(() => {
    request.post(
      'orders/api/get-statistics',
      { idOrder: form.id },
      {},
      (data: any) => { setStatistics(data); }
    )
  }, []);

  if (statistics) {
    let totalWorkedHours = 0;
    let totalChargeableHours = 0;
    let totalCostsByWorker = 0;

    if (!statistics.projects || statistics.projects.length == 0) return <div className='alert alert-warning'>Order has not project assigned yet.</div>;

    return <div>{Object.keys(statistics.projects).map((idProject) => {
      const P = statistics.projects[idProject];
      return <div className='card'>
        <div className='card-header'>{P.project.identifier} {P.project.title}</div>
        <div className='card-body gap-2'>
          <div className='card'>
            <div className='card-header'>{T.translate('Worked hours & costs by month')}</div>
            <div className='card-body'>
              <table className='table-default dense'>
                <tbody>
                  {P.workedByMonth.map((item, key) => {
                    totalWorkedHours += parseFloat(item.worked_hours);
                    return <tr key={key}>
                      <td>{item.year}-{item.month}</td>
                      <td>{item.worked_hours} {T.translate('hours')}</td>
                    </tr>;
                  })}
                </tbody>
                <tfoot>
                  <tr>
                    <td className='bg-primary text-white p-2'>{T.translate('Total')}</td>
                    <td className='bg-primary text-white p-2'>{globalThis.hubleto.numberFormat(totalWorkedHours)} {T.translate('hours')}</td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>

          <div className='card'>
            <div className='card-header'>{T.translate('Chargeable hours by month')}</div>
            <div className='card-body'>
              <table className='table-default dense'>
                <tbody>
                  {P.chargeableByMonth.map((item, key) => {
                    totalChargeableHours += parseFloat(item.worked_hours);
                    return <tr key={key}>
                      <td>{item.year}-{item.month}</td>
                      <td>{item.worked_hours} {T.translate('hours')}</td>
                    </tr>;
                  })}
                </tbody>
                <tfoot>
                  <tr>
                    <td className='bg-primary text-white p-2'>{T.translate('Total')}</td>
                    <td className='bg-primary text-white p-2'>{globalThis.hubleto.numberFormat(totalChargeableHours)} {T.translate('hours')}</td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
          <div className='card'>
            <div className='card-header'>{T.translate('Labor costs calculator')}</div>
            <div className='card-body'>
              <table className='table-default dense'>
                <thead>
                  <tr>
                    <th>{T.translate('User')}</th>
                    <th>{T.translate('Worked hours')}</th>
                    <th>{T.translate('Salary')}</th>
                    <th>{T.translate('Labor costs')}</th>
                  </tr>
                </thead>
                <tbody>
                  {P.workedByUser.map((item, key) => {
                    let workerCosts = item.worked_hours * salaries[item.id_worker];
                    totalCostsByWorker += workerCosts;
                    return <tr key={key}>
                      <td>{item.worker_name}</td>
                      <td>{item.worked_hours} {T.translate('hours')}</td>
                      <td><div className="flex gap-2 items-center">
                        <input
                          value={salaries[item.id_worker] ?? ''}
                          className="w-12 bg-white"
                          onChange={(e) => {
                            let newSalaries = salaries;
                            newSalaries[item.id_worker] = e.currentTarget.value;
                            setSalaries(newSalaries);
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
                    <td className='bg-primary text-white p-2'>{T.translate('Total')}</td>
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
    return <Spinner>Loading statistics...</Spinner>;
  }
}

/** TabHistory */
const TabHistory = (props: FormOrderProps) => {
  const form = React.useContext(FormMetaContext);
  if (form.id <= 0) return <div className='alert alert-info'>Create order first</div>;
  return <Table
    parentForm={form}
    uid={props.uid + "_table_order_history"}
    model={'Hubleto/App/Community/Orders/Models/History'}
    endpointParams={{idOrder: form.id}}
    readonly={true}
  ></Table>;
}

/** FormOrder */
const FormOrder = (props: FormOrderProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Order'}
    urlSlug='orders'
    title={{fields: ['identifier', 'title'], sub: <Input field='purchase_sales'  renderOnlyInputField customInputProps={{ uiStyle: 'buttons' }} />}}
    tabs={{
      default: {title: <b>{T.translate('Order')}</b>, content: () => <TabDefault {...props} />},
      items: {title: T.translate('Items'), content: () => <TabItems {...props} />},
      calendar: {title: T.translate('Calendar'), content: () => <TabCalendar {...props} />},
      quotes: {title: T.translate('Quotes'), content: () => <TabQuotes {...props} />},
      worksheet: {title: T.translate('Worksheet'), content: () => <TabWorksheet {...props} />},
      // invoicing: {title: T.translate('Invoicing'), content: () => <TabInvoicing {...props} />},
      statistics: {title: T.translate('Statistics'), content: () => <TabStatistics {...props} />},
      history: { title: T.translate('History'), content: () => <TabHistory {...props} /> },
   }}
    {...props}
  ></Form>;
}

export default FormOrder;
