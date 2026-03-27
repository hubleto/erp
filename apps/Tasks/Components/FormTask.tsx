import React, { Component } from 'react'
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import TableActivities from '@hubleto/apps/Worksheets/Components/TableActivities';
import FormInput from '@hubleto/react-ui/core/FormInput';
import moment from 'moment';
import UserSelect from '@hubleto/react-ui/core/Inputs/UserSelect';
import DateTime from '@hubleto/react-ui/core/Inputs/DateTime';

interface FormTaskProps extends FormExtendedProps {
  idCustomer?: any,
}
interface FormTaskState extends FormExtendedState {
  newTodo?: any,
}

export default class FormTask<P, S> extends FormExtended<FormTaskProps, FormTaskState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    icon: 'fas fa-list-check',
    model: 'Hubleto/App/Community/Tasks/Models/Task',
    renderWorkflowUi: true,
  }

  props: FormTaskProps;
  state: FormTaskState;

  translationContext: string = 'Hubleto\\App\\Community\\Tasks\\Loader';
  translationContextInner: string = 'Components\\FormTask';

  refInputNewTodoTodo: any = React.createRef();
  refInputNewTodoResponsible: any = React.createRef();
  refInputNewTodoDeadline: any = React.createRef();

  constructor(props: FormTaskProps) {
    super(props);
  }

  getStateFromProps(props: FormTaskProps) {
    return {
      newTodo: {todo: '', id_responsible: globalThis.hubleto.idUser, date_deadline: moment().add(1, 'week').format('YYYY-MM-DD')},
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Task','Hubleto\\App\\Community\\Tasks\\Loader','Components\\FormTask')}</b> },
        { uid: 'worksheet', title: this.translate('Worksheet','Hubleto\\App\\Community\\Tasks\\Loader','Components\\FormTask') },
        { uid: 'timeline', icon: 'fas fa-timeline', position: 'right' },
        ...this.getCustomTabs()
      ]
    }
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
      saveRelations: ['TODO'],
      idCustomer: this.props.idCustomer,
    }
  }

  getRecordFormUrl(): string {
    return 'tasks/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  contentClassName(): string
  {
    return this.state.record.is_closed ? 'bg-slate-100' : '';
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate('Task')}</small>
      <h2>{this.state.record.identifier ?? '-'}</h2>
    </>;
  }

  addTodo(todo: any, R: any) {
    if (todo.todo.trim() != '') {
      let newR = R;
      newR.TODO.push({ id_task: R.id, todo: todo.todo, id_responsible: todo.id_responsible, date_deadine: todo.date_deadline });
      this.refInputNewTodoTodo.current.value = '';
      this.refInputNewTodoResponsible.current.value = globalThis.hubleto.idUser;
      this.updateRecord(newR);
      this.setState({newTodo: {todo: '', id_responsible: globalThis.hubleto.idUser, date_deadline: moment().add(1, 'week').format('YYYY-MM-DD')}})
    }
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <>
          <div className='w-full flex gap-2 flex-col md:flex-row'>
            <div className='flex-1 border-r border-gray-100'>
              <div className='flex gap-2'>
                {R.ORDERS && R.ORDERS.length > 0 ?
                  <FormInput title={this.translate("Orders")}>{R.ORDERS.map((item, key) => {
                    return (item ? <a
                      key={key}
                      className='badge'
                      href={globalThis.hubleto.config.projectUrl + '/orders/' + item.id}
                      target='_blank'
                    >{item.identifier}</a> : '#');
                  })}</FormInput>
                  : null}
                {R.DEALS && R.DEALS.length > 0 ?
                  <FormInput title={this.translate("Deals")}>{R.DEALS.map((item, key) => {
                    return (item ? <a
                      key={key}
                      className='badge'
                      href={globalThis.hubleto.config.projectUrl + '/deals/' + item.id}
                      target='_blank'
                    >{item.identifier}</a> : '#');
                  })}</FormInput>
                : null}
                {R.PROJECTS && R.PROJECTS.length > 0 ?
                  <FormInput title={this.translate("Projects")}>{R.PROJECTS.map((item, key) => {
                    return (item ? <a
                      key={key}
                      className='badge'
                      href={globalThis.hubleto.config.projectUrl + '/projects/' + item.id}
                      target='_blank'
                    >{item.identifier}</a> : '#');
                  })}</FormInput>
                : null}
              </div>
              {this.inputWrapper('identifier', {cssClass: 'text-2xl'})}
              {this.inputWrapper('title', {cssClass: 'text-2xl'})}
              {this.inputWrapper('description')}
              {this.inputWrapper('shared_folder')}
              {this.inputWrapper('hours_estimation')}
            </div>
            <div className='flex-1'>
              {this.inputWrapper('id_customer')}
              {this.inputWrapper('id_contact')}
              {this.inputWrapper('id_developer')}
              {this.inputWrapper('id_tester')}
              {this.inputWrapper('priority')}
              {this.inputWrapper('duration_days')}
              {this.inputWrapper('date_start')}
              {this.inputWrapper('date_deadline')}
              {this.inputWrapper('is_chargeable')}
              {this.inputWrapper('is_milestone')}
              {this.inputWrapper('notes')}
              {this.inputWrapper('date_created')}
            </div>
            {this.state.id <= 0 ? null :
              <div className='flex-1'>
                <div className='card card-info'>
                  <div className='card-header'>
                    <div className="flex w-full justify-between">
                      <div>{this.translate('Todo')}</div>
                      <div className="text-sm">{this.translate("Press ENTER to add new Todo")}</div>
                    </div>
                  </div>
                  <div className='card-body btn-list'>
                    {R.TODO && R.TODO.map((item, key) => {
                      const refInputTodo = React.createRef();

                      return <div className={'btn-list-item items-center flex gap-2' + (item._toBeDeleted_ ? ' bg-red-100' : '')} key={key}>
                        <div>
                          <input
                            type='checkbox'
                            checked={item.is_closed}
                            onChange={(e) => {
                              let newR = R;
                              newR.TODO[key].is_closed = e.currentTarget.checked;
                              this.updateRecord(newR);
                            }}
                          ></input>
                        </div>
                        {item.is_closed ?
                          <div className='w-full line-through'>{item.todo}</div>
                        : <div className='w-full flex flex-col gap-2'>
                          <textarea
                            className={'w-full field-sizing-content dark:bg-slate-600 ' + (this.state.record.is_closed ? 'bg-slate-100 text-slate-400' : 'bg-yellow-50')}
                            readOnly={this.state.record.is_closed}
                            ref={refInputTodo}
                            value={item.todo}
                            placeholder={this.translate('What to do?')}
                            onChange={(e) => {
                              let newR = R;
                              newR.TODO[key].todo = refInputTodo.current.value;
                              this.updateRecord(newR);
                            }}
                          ></textarea>
                          <div className='flex gap-2'>
                            <div className='w-full'>
                              <UserSelect
                                uid='new_todo_id_responsible'
                                ref={this.refInputNewTodoResponsible}
                                value={item.id_responsible}
                                onChange={(input: any) => {
                                  let newR = R;
                                  newR.TODO[key].id_responsible = input.state.value;
                                  this.updateRecord(newR);
                                }}
                              />
                            </div>
                            <div className='w-full'>
                              <DateTime
                                uid='new_todo_deadline'
                                type='date'
                                ref={this.refInputNewTodoDeadline}
                                value={item.date_deadline}
                                onChange={(input: any) => {
                                  let newR = R;
                                  newR.TODO[key].date_deadline = input.state.value;
                                  this.updateRecord(newR);
                                }}
                              />
                            </div>
                          </div>
                        </div>}
                        <div>
                          {this.state.record.is_closed ? <></> :
                            <button
                              className={'btn ' + (item._toBeDeleted_ ? 'btn-primary' : 'btn-danger')}
                              onClick={(e) => {
                                let newR = R;
                                if (newR.TODO[key].id == undefined) {
                                  newR.TODO = newR.TODO.filter((todoItem: any, todoKey: number) => todoKey !== key);
                                } else {
                                  newR.TODO[key]._toBeDeleted_ = !newR.TODO[key]._toBeDeleted_;
                                }
                                this.updateRecord(newR);
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
                    {!this.state.record.is_closed ?
                      <div className='btn-list-item flex gap-2'>
                        <div className='flex flex-col gap-2'>
                          <textarea
                            className='w-full field-sizing-content bg-yellow-50 dark:bg-slate-600'
                            ref={this.refInputNewTodoTodo}
                            placeholder={this.translate('Add new todo...')}
                            onChange={(e) => {
                              this.setState({newTodo: {...this.state.newTodo, todo: this.refInputNewTodoTodo.current.value}});
                            }}
                            onKeyDown={(e) => {
                              if (e.key !== 'Enter' || e.shiftKey || e.ctrlKey) return;
                              e.preventDefault();
                              this.addTodo(this.state.newTodo, R);
                            }}
                          ></textarea>
                          <div className='flex gap-2'>
                            <div>
                              <UserSelect
                                uid='new_todo_id_responsible'
                                ref={this.refInputNewTodoResponsible}
                                value={this.state.newTodo.id_responsible ?? 0}
                                onChange={(input: any) => {
                                  this.setState({newTodo: {...this.state.newTodo, id_responsible: input.state.value}});
                                }}
                              />
                            </div>
                            <div>
                              <DateTime
                                uid='new_todo_deadline'
                                type='date'
                                ref={this.refInputNewTodoDeadline}
                                value={this.state.newTodo.date_deadline ?? moment().add(1, 'week').format('YYYY-MM-DD')}
                                onChange={(input: any) => {
                                  this.setState({newTodo: {...this.state.newTodo, idd_responsible: input.state.value}});
                                }}
                              />
                            </div>
                          </div>
                        </div>
                        <div>
                          <button
                            className='btn btn-add'
                            onClick={(e) => {
                              e.preventDefault();
                              this.addTodo(this.state.newTodo, R);
                            }}
                          ><span className='icon'><i className='fas fa-plus'></i></span></button>
                        </div>
                      </div>
                    : <></>}
                  </div>
                </div>
              </div>
            }
          </div>
        </>;
      break;

      case 'worksheet':
        return <TableActivities
          uid={this.props.uid + "_table_activities"}
          tag="TaskActivities"
          parentForm={this}
          idTask={R.id}
        />;
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

}
