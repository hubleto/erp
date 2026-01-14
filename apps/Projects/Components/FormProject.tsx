import React, { Component } from 'react'
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import TableTasks from '@hubleto/apps/Tasks/Components/TableTasks';
import TableActivities from '@hubleto/apps/Worksheets/Components/TableActivities';
import FormInput from '@hubleto/react-ui/core/FormInput';
import request from '@hubleto/react-ui/core/Request';
import Lookup from '@hubleto/react-ui/core/Inputs/Lookup';
import { ProgressBar } from 'primereact/progressbar';

export interface FormProjectProps extends FormExtendedProps { }
export interface FormProjectState extends FormExtendedState {
  statistics?: any,
  selectParentOrder?: boolean,
}

export default class FormProject<P, S> extends FormExtended<FormProjectProps, FormProjectState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    icon: 'fas fa-diagram-project',
    model: 'Hubleto/App/Community/Projects/Models/Team',
    renderWorkflowUi: true,
  }

  props: FormProjectProps;
  state: FormProjectState;

  translationContext: string = 'Hubleto\\App\\Community\\Projects\\Loader';
  translationContextInner: string = 'Components\\FormProject';

  constructor(props: FormProjectProps) {
    super(props);
  }

  getStateFromProps(props: FormProjectProps) {
    return {
      ...super.getStateFromProps(props),
      selectParentOrder: false,
      tabs: [
        { uid: 'default', title: <b>{this.translate('Project')}</b> },
        { uid: 'tasks', title: this.translate('Tasks'), showCountFor: 'TASKS' },
        { uid: 'worksheet', title: this.translate('Worksheet') },
        { uid: 'statistics', title: this.translate('Statistics') },
        { uid: 'timeline', icon: 'fas fa-timeline', position: 'right' },
        ...this.getCustomTabs()
      ]
    }
  }

  getRecordFormUrl(): string {
    return 'projects/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  contentClassName(): string
  {
    return this.state.record.is_closed ? 'bg-slate-100' : '';
  }

  onTabChange() {
    const tabUid = this.state.activeTabUid;
    switch (tabUid) {
      case 'statistics':
        request.post(
          'projects/api/get-statistics',
          { idProject: this.state.record.id },
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
      <small>{this.translate('Project')}</small>
      <h2>{this.state.record.identifier ?? '-'}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <>
          <div className='w-full flex gap-2 flex-col md:flex-row'>
            <div className='flex-1 border-r border-gray-100'>
              <FormInput title={"Order"}>
                {this.state.selectParentOrder ? <>
                  <Lookup
                    model='Hubleto/App/Community/Orders/Models/Order'
                    cssClass='font-bold'
                    onChange={(input: any, value: any) => {
                      request.post(
                        'projects/api/set-parent-order',
                        {
                          idProject: this.state.record.id,
                          idOrder: value,
                        },
                        {},
                        (data: any) => {
                          this.setState({selectParentOrder: false}, () => this.reload());
                        }
                      )
                    }}
                  ></Lookup>
                </> : <>
                  {R.ORDERS ? R.ORDERS.map((item, key) => {
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
                    onClick={() => {
                      this.setState({selectParentOrder: true});
                    }}
                  >
                    <span className='text'>{this.translate('Select parent order')}</span>
                  </button>
                </>}
              </FormInput>
              {this.inputWrapper('identifier', {cssClass: 'text-2xl'})}
              {this.inputWrapper('title', {cssClass: 'text-2xl'})}
              {this.inputWrapper('description')}
              {this.inputWrapper('id_main_developer')}
              {this.inputWrapper('id_project_manager')}
              {this.inputWrapper('id_account_manager')}
              {this.inputWrapper('priority')}
              {this.inputWrapper('date_start')}
              {this.inputWrapper('date_deadline')}
              {this.inputWrapper('budget')}
              {/* {this.inputWrapper('is_closed')} */}
            </div>
            <div className='flex-1'>
              {R.id > 0 ?
                <div className='card card-info'>
                  <div className='card-header'>{this.translate('Open tasks')}</div>
                  <div className='card-body'>
                    <TableTasks
                      tag={"table_project_task"}
                      parentForm={this}
                      uid={this.props.uid + "_table_project_task"}
                      junctionTitle='Project'
                      junctionModel='Hubleto/App/Community/Projects/Models/ProjectTask'
                      junctionSourceColumn='id_project'
                      junctionSourceRecordId={R.id}
                      junctionDestinationColumn='id_task'
                      view='briefOverview'
                    />
                  </div>
                </div>
              : null}
              {this.inputWrapper('id_customer')}
              {this.inputWrapper('id_contact')}
              {this.inputWrapper('color')}
              {this.inputWrapper('online_documentation_folder')}
              {this.inputWrapper('notes')}
              {this.inputWrapper('average_hourly_costs')}
              {/* {this.inputWrapper('id_deal')} */}
            </div>
          </div>
        </>;
      break;

      case 'tasks':
        try {
          return <>
            {this.state.id < 0 ?
                <div className="badge badge-info">First create the project, then you will be prompted to add tasks.</div>
              :
                <TableTasks
                  tag={"table_project_task"}
                  parentForm={this}
                  uid={this.props.uid + "_table_project_task"}
                  junctionTitle='Project'
                  junctionModel='Hubleto/App/Community/Projects/Models/ProjectTask'
                  junctionSourceColumn='id_project'
                  junctionSourceRecordId={R.id}
                  junctionDestinationColumn='id_task'
                />
            }
          </>;
        } catch (ex) {
          return <div className="alert alert-error">Failed to display tasks. Check if you have 'Tasks' app installed.</div>
        }
      break;

      case 'worksheet':
        return <TableActivities
          uid={this.props.uid + "_table_activities"}
          tag="ProjectActivities"
          parentForm={this}
          idProject={R.id}
          readonly={true}
        />;
      break;

      case 'statistics':
        if (this.state.statistics) {
          let totalWorkedHours = 0;
          let totalCosts = 0;
          return <div className='flex gap-2'>
            <div className='card'>
              <div className='card-header'>Worked hours & costs by month</div>
              <div className='card-body'>
                <table className='table-default dense'>
                  <tbody>
                    {this.state.statistics.workedByMonth.map((item, key) => {
                      totalWorkedHours += parseFloat(item.worked_hours);
                      totalCosts += parseFloat(item.costs);
                      return <tr key={key}>
                        <td>{item.year}-{item.month}</td>
                        <td>{item.worked_hours} hours</td>
                        <td>{globalThis.hubleto.numberFormat(item.costs, 2, ",", " ")}&nbsp;{globalThis.hubleto.currencySymbol}</td>
                      </tr>;
                    })}
                  </tbody>
                  <tfoot>
                    <tr>
                      <td className='bg-primary text-white p-2'>{this.translate('Total')}</td>
                      <td className='bg-primary text-white p-2'>{totalWorkedHours} hours</td>
                      <td className='bg-primary text-white p-2'>{globalThis.hubleto.numberFormat(totalCosts, 2, ",", " ")}&nbsp;{globalThis.hubleto.currencySymbol}</td>
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
                    {this.state.statistics.chargeableByMonth.map((item, key) => {
                      return <tr key={key}>
                        <td>{item.year}-{item.month}</td>
                        <td>{item.worked_hours} hours</td>
                      </tr>;
                    })}
                  </tbody>
                </table>
              </div>
            </div>
          </div>;
        } else {
          return <ProgressBar mode="indeterminate" style={{ height: '8px' }}></ProgressBar>;
        }
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
