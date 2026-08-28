import React, { useState, useEffect } from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormMeta, FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import { useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';
import request from '@hubleto/react-ui/core/Request';
import TableMilestones from './TableMilestones';
import LookupInput from '@hubleto/react-ui/components/fc/Inputs/Lookup';
import TableTasks from '@hubleto/apps/Tasks/Components/FC/TableTasks';
import TableActivities from '@hubleto/apps/Worksheets/Components/FC/TableActivities';
import TableExpenses from './TableExpenses';
import Spinner from '@hubleto/react-ui/components/fc/Spinner';

export interface FormProjectProps extends FormProps {}

const componentName = 'FormProject'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Projects';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormProjectProps) => {
  const form = React.useContext(FormMetaContext);
  const ORDERS: any = useRecordField('ORDERS', {});
  const idCustomer: number = useRecordField('id_customer', 0);

  const [selectParentOrder, setSelectParentOrder] = useState(false);

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
      <div className='flex-1 border-r border-gray-100'>
        <Input title={T.translate("Order")}>
          {selectParentOrder ? <LookupInput
            model='Hubleto/App/Community/Orders/Models/Order'
            cssClass='font-bold'
            onChange={(input: any, value: any) => {
              request.post(
                'projects/api/set-parent-order',
                { idProject: form.id, idOrder: value },
                {},
                (data: any) => { setSelectParentOrder(false); }
              )
            }}
          ></LookupInput>
          : <>
            {ORDERS ? ORDERS.map((item, key) => {
              if (!item.ORDER) return null;
              return (item.ORDER ? <a
                key={key}
                className='badge'
                href={globalThis.hubleto.config.projectUrl + '/orders/' + item.ORDER.id}
                target='_blank'
              >#{item.ORDER.identifier}&nbsp;{item.ORDER.title}</a> : '#');
            }) : null}
            <button
              className='btn btn-small btn-transparent'
              onClick={() => { setSelectParentOrder(true); }}
            >
              <span className='text'>{T.translate('Select parent order')}</span>
            </button>
          </>}
        </Input>
        <Input field='description' />
        <Input field='id_main_developer' />
        <Input field='id_project_manager' />
        <Input field='id_account_manager' />
        <Input field='priority' />
        <Input field='date_start' />
        <Input field='date_deadline' />
        <Input field='budget' />
        {/* <Input field='is_closed' /> */}
      </div>
      <div className='flex-1'>
        {form.id > 0 ? <>
          <div className='card card-warning'>
            <div className='card-header'>{T.translate('Milestones')}</div>
            <div className='card-body'>
              <TableMilestones
                tag={"table_project_task"}
                parentForm={form}
                uid={props.uid + "_table_project_task"}
                idProject={form.id}
                view='briefOverview'
              />
            </div>
          </div>
          <div className='card card-info mt-2'>
            <div className='card-header'>{T.translate('Open tasks')}</div>
            <div className='card-body'>
              <TableTasks
                tag={"table_project_task"}
                parentForm={form}
                uid={props.uid + "_table_project_task"}
                junctionTitle='Project'
                junctionModel='Hubleto/App/Community/Projects/Models/ProjectTask'
                junctionSourceColumn='id_project'
                junctionSourceRecordId={form.id}
                junctionDestinationColumn='id_task'
                view='briefOverview'
              />
            </div>
          </div>
        </> : null}
        <Input field='id_customer' />
        <Input field='id_contact' />
        <Input field='notes' />
        <Input field='average_hourly_costs' />
        {/* <Input field='id_deal' /> */}
      </div>
    </div>
  </>;
}

/** TabDocuments */
const TabDocuments = (props: FormProjectProps) => {
  const onlineDocumentationFolder: string = useRecordField('online_documentation_folder', '');
  const sharedFolder: string = useRecordField('shared_folder', '');

  let iframeUrl = '';

  try {
    let url = new URL(onlineDocumentationFolder);
    
    if (
      url.hostname == 'drive.google.com'
      && url.pathname.indexOf('/drive/folders') == 0
    ) {

      // for Google Drive, replacing
      // https://drive.google.com/drive/folders/FOLDER_ID
      // with https://drive.google.com/embeddedfolderview?id=FOLDER_ID
      // makes the folder embeddable

      iframeUrl = 'https://drive.google.com/embeddedfolderview'
        + '?id=' + url.pathname.replace('/drive/folders/', '')
        + '&authuser=0'
      ;

    } else {
      iframeUrl = sharedFolder;
    }
  } catch (e) {
  }

  return <div className='flex flex-col gap-2 h-full'>
    <Input field='online_documentation_folder' />
    <iframe
      className='w-full h-full shadow-sm'
      src={iframeUrl}
    ></iframe>
  </div>;
}

/** TabMilestones */
const TabMilestones = (props: FormProjectProps) => {
  const form = React.useContext(FormMetaContext);
  return (form.id < 0
    ? <div className="badge badge-info">{T.translate('First create the project, then you will be prompted to add tasks.')}</div>
    : <TableMilestones
      tag={"table_project_milestone"}
      parentForm={form}
      uid={props.uid + "_table_project_milestone"}
      idProject={form.id}
    />
  );
}

/** TabTasks */
const TabTasks = (props: FormProjectProps) => {
  const form = React.useContext(FormMetaContext);

  return (form.id < 0
    ? <div className="badge badge-info">{T.translate('First create the project, then you will be prompted to add tasks.')}</div>
    : <TableTasks
      tag={"table_project_task"}
      parentForm={form}
      uid={props.uid + "_table_project_task"}
      junctionTitle='Project'
      junctionModel='Hubleto/App/Community/Projects/Models/ProjectTask'
      junctionSourceColumn='id_project'
      junctionSourceRecordId={form.id}
      junctionDestinationColumn='id_task'
    />
  );
}

/** TabWorksheet */
const TabWorksheet = (props: FormProjectProps) => {
  const form = React.useContext(FormMetaContext);
  return <TableActivities
    uid={props.uid + "_table_activities"}
    tag="ProjectActivities"
    parentForm={form}
    idProject={form.id}
    readonly={true}
  />;
}

/** TabExpenses */
const TabExpenses = (props: FormProjectProps) => {
  const form = React.useContext(FormMetaContext);
  return (form.id < 0
    ? <div className="badge badge-info">{T.translate('First create the project, then you will be prompted to add tasks.')}</div>
    : <TableExpenses
      tag={"table_project_expense"}
      parentForm={form}
      uid={props.uid + "_table_project_expense"}
      idProject={form.id}
    />
  );
}

/** TabStatistics */
const TabStatistics = (props: FormProjectProps) => {
  const form = React.useContext(FormMetaContext);

  const [statistics, setStatistics] = useState(null);
  const [salaries, setSalaries] = useState(null);

  useEffect(() => {
    request.post(
      'projects/api/get-statistics',
      { idProject: form.id },
      {},
      (data: any) => {
        setStatistics(data);
      }
    )
  }, []);

  if (statistics) {
    let totalWorkedHours = 0;
    let totalChargeableHours = 0;
    let totalCostsByWorker = 0;
    return <div>
      <div className='flex gap-2'>
        <div className='card'>
          <div className='card-header'>{T.translate('Worked hours & costs by month')}</div>
          <div className='card-body'>
            <table className='table-default dense'>
              <tbody>
                {statistics.workedByMonth.map((item, key) => {
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
                {statistics.chargeableByMonth.map((item, key) => {
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
      </div>

      <div className="flex gap-2 mt-2">
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
                {statistics.workedByUser.map((item, key) => {
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
  } else {
    return <Spinner></Spinner>;
  }
}

/** FormProject */
const FormProject = (props: FormProjectProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Project'}
    urlSlug='projects'
    title={{fields: ['identifier', 'title'], sub: T.translate('Project')}}
    tabs={{
      default: {title: <b>{T.translate('Project')}</b>, content: () => <TabDefault {...props} /> },
      documents: {title: T.translate('Documents'), content: () => <TabDocuments {...props} /> },
      milestones: {title: T.translate('Milestones'), content: () => <TabMilestones {...props} /> },
      tasks: {title: T.translate('Tasks'), content: () => <TabTasks {...props} /> },
      worksheet: {title: T.translate('Worksheet'), content: () => <TabWorksheet {...props} /> },
      expenses: {title: T.translate('Expenses'), content: () => <TabExpenses {...props} /> },
      statistics: {title: T.translate('Statistics'), content: () => <TabStatistics {...props} /> },
    }}
    {...props}
  ></Form>;
}

export default FormProject;
