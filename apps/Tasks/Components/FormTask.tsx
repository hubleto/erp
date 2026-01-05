import React, { Component } from 'react'
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import TableActivities from '@hubleto/apps/Worksheets/Components/TableActivities';
import FormInput from '@hubleto/react-ui/core/FormInput';

interface FormTaskProps extends HubletoFormProps { }
interface FormTaskState extends HubletoFormState {
  newTodo?: string,
}

export default class FormTask<P, S> extends HubletoForm<FormTaskProps, FormTaskState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    icon: 'fas fa-list-check',
    model: 'Hubleto/App/Community/Tasks/Models/Task',
    renderWorkflowUi: true,
  }

  props: FormTaskProps;
  state: FormTaskState;

  translationContext: string = 'Hubleto\\App\\Community\\Tasks\\Loader';
  translationContextInner: string = 'Components\\FormTask';

  refInputNewTodo: any;

  constructor(props: FormTaskProps) {
    super(props);
    this.refInputNewTodo = React.createRef();
  }

  getStateFromProps(props: FormTaskProps) {
    return {
      newTodo: '',
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Task')}</b> },
        { uid: 'worksheet', title: this.translate('Worksheet') },
        { uid: 'timeline', icon: 'fas fa-timeline', position: 'right' },
        ...this.getCustomTabs()
      ]
    }
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
      saveRelations: ['TODO'],
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

  // renderTopMenu() {
  //   return <>
  //     {super.renderTopMenu()}
  //     {this.state.id <= 0 ? null : <>
  //       <div className='flex-2 pl-4'><WorkflowSelector parentForm={this}></WorkflowSelector></div>
  //       {this.inputWrapper('is_closed', {wrapperCssClass: 'flex gap-2'})}
  //     </>}
  //   </>
  // }

  addTodo(value: string, R: any) {
    if (value.trim() != '') {
      let newR = R;
      newR.TODO.push({ id_task: R.id, todo: value });
      this.refInputNewTodo.current.value = '';
      this.updateRecord(newR);
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
                  <FormInput title={"Orders"}>{R.ORDERS.map((item, key) => {
                    return (item ? <a
                      key={key}
                      className='badge'
                      href={globalThis.hubleto.config.projectUrl + '/orders/' + item.id}
                      target='_blank'
                    >{item.identifier}</a> : '#');
                  })}</FormInput>
                  : null}
                {R.DEALS && R.DEALS.length > 0 ?
                  <FormInput title={"Deals"}>{R.DEALS.map((item, key) => {
                    return (item ? <a
                      key={key}
                      className='badge'
                      href={globalThis.hubleto.config.projectUrl + '/deals/' + item.id}
                      target='_blank'
                    >{item.identifier}</a> : '#');
                  })}</FormInput>
                : null}
                {R.PROJECTS && R.PROJECTS.length > 0 ?
                  <FormInput title={"Projects"}>{R.PROJECTS.map((item, key) => {
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
                        :
                          <div className='w-full'>
                            <textarea
                              className={'w-full field-sizing-content dark:bg-slate-600 ' + (this.state.record.is_closed ? 'bg-slate-100 text-slate-400' : 'bg-yellow-50')}
                              readOnly={this.state.record.is_closed}
                              ref={refInputTodo}
                              value={item.todo}
                              placeholder='What to do?'
                              onChange={(e) => {
                                let newR = R;
                                newR.TODO[key].todo = refInputTodo.current.value;
                                this.updateRecord(newR);
                              }}
                            ></textarea>
                          </div>
                        }
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
                        <div className='w-full'>
                          <textarea
                            className='w-full field-sizing-content bg-yellow-50 dark:bg-slate-600'
                            ref={this.refInputNewTodo}
                            placeholder='Add new todo...'
                            onChange={(e) => {
                              this.setState({newTodo: this.refInputNewTodo.current.value});
                            }}
                            onKeyDown={(e) => {
                              if (e.key !== 'Enter' || e.shiftKey || e.ctrlKey) return;
                              e.preventDefault();
                              this.addTodo(this.state.newTodo, R);
                              this.setState({newTodo: ""});
                            }}
                          ></textarea>
                        </div>
                        <div>
                          <button
                            className='btn btn-success'
                            onClick={(e) => {
                              e.preventDefault();
                              this.addTodo(this.state.newTodo, R);
                              this.setState({newTodo: ""});
                            }}
                          ><span className='icon'><i className='fas fa-check'></i></span></button>
                        </div>
                      </div>
                    : <></>}
                    {/* <button
                      className='btn btn-transparent'
                      onClick={() => {
                        let newR = R;
                        newR.TODO.push({
                          id_task: R.id,
                          todo: '',
                          is_closed: false,
                        })
                        this.updateRecord(newR);
                      }}
                    >
                      <span className='icon'><i className='fas fa-plus'></i></span>
                      <span className='text'>{this.translate('Add todo')}</span>
                    </button> */}
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
