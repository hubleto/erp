import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import { useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';
import moment from 'moment';
import DateTimeInput from '@hubleto/react-ui/components/fc/Inputs/DateTime';
import UserSelectInput from '@hubleto/react-ui/components/fc/Inputs/UserSelect';
import TableActivities from '@hubleto/apps/Worksheets/Components/FC/TableActivities';

export interface FormTaskProps extends FormProps {}

const componentName = 'FormTask'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Tasks';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormTaskProps) => {
  const form = React.useContext(FormMetaContext);

  const ORDERS: any = useRecordField('ORDERS', {});
  const DEALS: any = useRecordField('DEALS', {});
  const PROJECTS: any = useRecordField('PROJECTS', {});
  const TODO: any = useRecordField('TODO', {});
  const isClosed: boolean = useRecordField('is_closed', false);
  const virtWorkedHours: number = useRecordField('virt_worked_hours', 0);

  return <div className='w-full flex gap-2 flex-col md:flex-row'>
    <div className='flex-2'>
      <div className='flex gap-2 flex-col md:flex-row'>
        <div className='flex-1 border-r border-gray-100'>
          <div className='flex gap-2'>
            {ORDERS && ORDERS.length > 0 ?
              <Input title={T.translate("Orders")}>{ORDERS.map((item, key) => {
                return (item ? <a
                  key={key}
                  className='badge badge-violet text-lg'
                  href={globalThis.hubleto.config.projectUrl + '/orders/' + item.id}
                  target='_blank'
                >{item.identifier} {item.title}</a> : '#');
              })}</Input>
              : null}
            {DEALS && DEALS.length > 0 ?
              <Input title={T.translate("Deals")}>{DEALS.map((item, key) => {
                return (item ? <a
                  key={key}
                  className='badge badge-violet text-lg'
                  href={globalThis.hubleto.config.projectUrl + '/deals/' + item.id}
                  target='_blank'
                >{item.identifier} {item.title}</a> : '#');
              })}</Input>
            : null}
            {PROJECTS && PROJECTS.length > 0 ?
              <Input title={T.translate("Projects")}>{PROJECTS.map((item, key) => {
                return (item ? <a
                  key={key}
                  className='badge badge-violet text-lg'
                  href={globalThis.hubleto.config.projectUrl + '/projects/' + item.id}
                  target='_blank'
                >{item.identifier} {item.title}</a> : '#');
              })}</Input>
            : null}
          </div>
          <Input field='identifier' customInputProps={{cssClass: 'text-2xl'}} />
          <Input field='title' customInputProps={{cssClass: 'text-2xl'}} />
          <Input field='description' />
          <Input field='shared_folder' />
          <Input field='hours_estimation' />
        </div>
        <div className='flex-1'>
          <Input field='id_customer' />
          <Input field='id_contact' />
          <Input field='id_developer' />
          <Input field='id_tester' />
          <Input field='priority' />
          <Input field='duration_days' />
          <Input field='date_start' />
          <Input field='date_deadline' />
          <Input field='is_chargeable' customInputProps={{yesText: 'Chargeable'}} />
          <Input field='is_milestone' customInputProps={{yesText: 'Milestone'}}  />
        </div>
      </div>
      <div>
        <Input field='notes' customInputProps={{cssClass: 'border border-orange-200'}} />
      </div>
    </div>
    {props.id <= 0 ? null : 
      <div className='flex-1 flex gap-2 flex-col'>
        <div className='text-2xl'>
          {globalThis.hubleto.numberFormat(virtWorkedHours, 2)} h
        </div>
        <div className='card card-info'>
          <div className='card-header'>
            <div className="flex w-full justify-between">
              <div>{T.translate('Todo')}</div>
            </div>
          </div>
          <div className='card-body btn-list'>
            {TODO && TODO.map((item, key) => {
              const refInputTodo = React.createRef();

              return <div className={'btn-list-item items-center flex gap-2 items-start' + (item._toBeDeleted_ ? ' bg-red-100' : '')} key={key}>
                <div>
                  <input
                    type='checkbox'
                    checked={item.is_closed}
                    onChange={(e) => {
                      let newTodo = TODO;
                      newTodo[key].is_closed = e.currentTarget.checked;
                      form.changeRecord({TODO: newTodo});
                    }}
                  ></input>
                </div>
                {item.is_closed ?
                  <div className='w-full line-through'>{item.todo}</div>
                : <div className='w-full flex flex-col gap-2'>
                  <textarea
                    className={
                      'w-full field-sizing-content dark:bg-slate-600 '
                      + (isClosed ? 'bg-slate-100 text-slate-400' : 'bg-yellow-50')
                    }
                    readOnly={isClosed}
                    value={item.todo}
                    placeholder={T.translate('What to do?')}
                    onChange={(e) => {
                      let newTodo = TODO;
                      newTodo[key].todo = e.currentTarget.value;
                      form.changeRecord({TODO: newTodo});
                    }}
                  ></textarea>
                  <div className='flex gap-2'>
                    <div className='w-full'>
                      <UserSelectInput
                        uid='new_todo_id_responsible'
                        value={item.id_responsible}
                        onChange={(input: any) => {
                          let newTodo = TODO;
                          newTodo[key].id_responsible = input.value;
                          form.changeRecord({TODO: newTodo});
                        }}
                      />
                    </div>
                    <div className='w-full'>
                      <DateTimeInput
                        uid='new_todo_deadline'
                        type='date'
                        value={item.date_deadline}
                        onChange={(input: any) => {
                          let newTodo = TODO;
                          newTodo[key].date_deadline = input.state.value;
                          form.changeRecord({TODO: newTodo});
                        }}
                      />
                    </div>
                  </div>
                </div>}
                <div>
                  {isClosed ? null :
                    <button
                      className={'btn ' + (item._toBeDeleted_ ? 'btn-primary' : 'btn-danger')}
                      onClick={(e) => {
                        let newTodo = TODO;
                        if (newTodo[key].id == undefined) {
                          newTodo = newTodo.filter((todoItem: any, todoKey: number) => todoKey !== key);
                        } else {
                          newTodo[key]._toBeDeleted_ = !newTodo[key]._toBeDeleted_;
                        }
                        form.changeRecord({TODO: newTodo});
                      }}
                    >
                      <span className='icon'>
                        <i className={'fas ' + (item._toBeDeleted_ ? 'fa-times' : 'fa-trash-can')}></i>
                      </span>
                    </button>
                  }

                </div>
              </div>;
            })}
            {!isClosed ?
              <button
                className='btn btn-add-outline'
                onClick={(e) => {
                  e.preventDefault();
                  let newTodo = TODO;
                  newTodo.push({
                    id_task: props.id,
                    todo: '',
                    id_responsible: globalThis.hubleto.idUser,
                    date_deadline: moment().add(1, 'week').format('YYYY-MM-DD')
                  });
                  form.changeRecord({TODO: newTodo});
                }}
              >
                <span className='icon'><i className='fas fa-plus'></i></span>
                <span className='text'>{T.translate('Add todo')}</span>
              </button>
            : <></>}
          </div>
        </div>
      </div>
    }
  </div>;
}

/** TabWorksheet */
const TabWorksheet = (props: FormTaskProps) => {
  const form = React.useContext(FormMetaContext);
  return <TableActivities
    uid={props.uid + "_table_activities"}
    tag="TaskActivities"
    parentForm={form}
    idTask={props.id}
  />;
}

/** FormTask */
const FormTask = (props: FormTaskProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/XXX'}
    urlSlug='tasks'
    endpointParams={{saveRelations: ['TODO']}}
    // onAfterFormInitialized={(form: any) => {}}
    // renderTitle={(): React.JSX.Element => { return <></>; }
    title={{fields: ['identifier', 'title'], sub: T.translate('Task')}}
    tabs={{
      default: {title: <b>{T.translate('Task')}</b>, content: () => <TabDefault {...props} /> },
      worksheet: {title: T.translate('Worksheet'), content: () => <TabWorksheet {...props} /> },
    }}
    {...props}
  ></Form>;
}

export default FormTask;
