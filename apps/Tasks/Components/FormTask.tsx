import React, { Component } from 'react'
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import WorkflowSelector from '@hubleto/apps/Workflow/Components/WorkflowSelector';
import TableActivities from '@hubleto/apps/Worksheets/Components/TableActivities';
import FormInput from '@hubleto/react-ui/core/FormInput';

interface FormTaskProps extends HubletoFormProps { }
interface FormTaskState extends HubletoFormState { }

export default class FormTask<P, S> extends HubletoForm<FormTaskProps, FormTaskState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'Hubleto/App/Community/Tasks/Models/Task',
  }

  props: FormTaskProps;
  state: FormTaskState;

  translationContext: string = 'Hubleto\\App\\Community\\Tasks::Components\\FormTask';

  constructor(props: FormTaskProps) {
    super(props);
  }

  getStateFromProps(props: FormTaskProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Task')}</b> },
        { uid: 'worksheet', title: this.translate('Worksheet') },
        ...(this.getParentApp()?.getFormTabs() ?? [])
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
    return this.state.record.is_closed ? 'opacity-85 bg-slate-100' : '';
  }

  renderTitle(): JSX.Element {
    return <>
      <small>Task</small>
      <h2>{this.state.record.identifier ?? '-'}</h2>
    </>;
  }

  renderTopMenu() {
    return <>
      {super.renderTopMenu()}
      {this.state.id <= 0 ? null : <div className='flex-2 pl-4'><WorkflowSelector parentForm={this}></WorkflowSelector></div>}
      {this.inputWrapper('is_closed', {wrapperCssClass: 'flex gap-2'})}
    </>
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <>
          <div className='w-full flex gap-2 flex-col md:flex-row'>
            <div className='flex-1 border-r border-gray-100'>
              <div className='flex gap-2'>
                {R.DEALS && R.DEALS.length > 0 ?
                  <FormInput title={"Projects"}>{R.DEALS.map((item, key) => {
                    return (item.DEAL ? <a
                      key={key}
                      className='badge'
                      href={globalThis.main.config.projectUrl + '/deals/' + item.DEAL.id}
                      target='_blank'
                    >{item.DEAL.identifier}</a> : '#');
                  })}</FormInput>
                : null}
                {R.PROJECTS && R.PROJECTS.length > 0 ?
                  <FormInput title={"Projects"}>{R.PROJECTS.map((item, key) => {
                    return (item.PROJECT ? <a
                      key={key}
                      className='badge'
                      href={globalThis.main.config.projectUrl + '/projects/' + item.PROJECT.id}
                      target='_blank'
                    >{item.PROJECT.identifier}</a> : '#');
                  })}</FormInput>
                : null}
              </div>
              {this.inputWrapper('identifier')}
              {this.inputWrapper('title')}
              {this.inputWrapper('description')}
              {this.inputWrapper('id_developer')}
              {this.inputWrapper('id_tester')}
              {this.inputWrapper('shared_folder')}
              {this.inputWrapper('hours_estimation')}
            </div>
            <div className='flex-1'>
              {this.inputWrapper('id_customer')}
              {this.inputWrapper('id_contact')}
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
                  <div className='card-header'>Todo</div>
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
                        <div className='w-full'>
                          <input
                            className={'w-full ' + (item.is_closed ? 'bg-slate-200' : 'bg-white')}
                            readOnly={item.is_closed}
                            ref={refInputTodo}
                            value={item.todo}
                            placeholder='What to do?'
                            onChange={(e) => {
                              let newR = R;
                              newR.TODO[key].todo = refInputTodo.current.value;
                              this.updateRecord(newR);
                            }}
                          ></input>
                        </div>
                        <div>
                          <button
                            className='btn btn-transparent'
                            onClick={(e) => {
                              let newR = R;
                              newR.TODO[key]._toBeDeleted_ = true;
                              this.updateRecord(newR);
                            }}
                          ><span className='icon'><i className='fas fa-times'></i></span></button>
                        </div>
                      </div>;
                    })}
                    <button
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
                      <span className='text'>Add todo</span>
                    </button>
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

      default:
        super.renderTab(tabUid);
      break;
    }
  }

}
