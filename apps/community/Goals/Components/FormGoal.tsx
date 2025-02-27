import React, { Component } from 'react';
import HubletoForm, {HubletoFormProps, HubletoFormState} from '@hubleto/src/core/Components/HubletoForm';
import { getUrlParam } from 'adios/Helper';
import ReportGoal from './ReportGoal';
import TableGoalValues from './TableGoalValues';
import request from 'adios/Request';
import FormInput from 'adios/FormInput';
import Boolean from 'adios/Inputs/Boolean';

export interface FormGoalProps extends HubletoFormProps {}

export interface FormGoalState extends HubletoFormState {
  showIntervals: boolean,
  deleteIntervals: boolean,
}

export default class FormGoal<P, S> extends HubletoForm<FormGoalProps,FormGoalState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Deals/Models/Deal',
  };

  props: FormGoalProps;
  state: FormGoalState;

  translationContext: string = 'HubletoApp\\Community\\Goals\\Loader::Components\\FormGoal';

  constructor(props: FormGoalProps) {
    super(props);
    this.state = {
      ...this.getStateFromProps(props),
      showIntervals: true,
      deleteIntervals: false,
      customEndpointParams: {deleteIntervals: this.state.deleteIntervals},
    }
  }

  getEndpointParams(): object {
    return {
      ...super.getEndpointParams(),
      deleteIntervals: this.state.deleteIntervals,
    }
  }

  getStateFromProps(props: FormGoalProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  renderTitle(): JSX.Element {
    if (getUrlParam('recordId') == -1) {
      return <h2>{this.translate('New Goal')}</h2>;
    } else {
      return <h2>{this.state.record.title ? this.state.record.title : '[Undefined Goal Title]'}</h2>
    }
  }

  getIntervalData(record) {
    request.get(
      'goals/report/get-interval-data',
      {
        interval: [record.date_start, record.date_end],
        frequency: record.frequency,
      },
      (data: any) => {
        if (data.status == "success") {
          data.data.map((item, index) => {
            item.id_goal = { _useMasterRecordId_: true }
            item.goal = 0;
            item.frequency = 1;
          })
          record.GOALS = data.data;
          this.setState({record: record});
        }
      }
    );
  }

  renderContent(): JSX.Element {
    const R = this.state.record;
    const showAdditional = R.id > 0 ? true : false;

    return (
      <>
        <div className='card'>
          <div className='card-header'>
            <h2>Goal Detail</h2>
          </div>
          <div className='card-body flex flex-row  justify-around'>
            <div>
              {this.inputWrapper("title")}
              {this.inputWrapper("id_user")}
              {this.inputWrapper("id_pipeline")}
              <div className='flex flew-row gap-2'>
                {this.inputWrapper("date_start", {onChange: () => {
                  if (this.state.record.is_individual_goals == 1) {
                    this.setState({deleteIntervals: true} as FormGoalState);
                    this.getIntervalData(this.state.record);
                  }
                }})}
                {this.inputWrapper("date_end", {onChange: () => {
                  if (this.state.record.is_individual_goals == 1) {
                    this.setState({deleteIntervals: true} as FormGoalState);
                    this.getIntervalData(this.state.record);
                  }
                }})}
              </div>
            </div>
            <div>
              {this.inputWrapper("frequency", {onChange: () => {
                if (this.state.record.is_individual_goals == 1) {
                  this.setState({deleteIntervals: true} as FormGoalState);
                  this.getIntervalData(this.state.record);
                }
              }})}
              {this.inputWrapper("metric")}
              {this.inputWrapper("goal")}
              <FormInput required={true} title={"Set individual goals?"}>
                <Boolean
                  uid={this.props.uid + "_input_goals"}
                  value={R.is_individual_goals}
                  isInlineEditing={this.state.isInlineEditing}
                  onChange={(value: any) => {
                    this.updateRecord({is_individual_goals: value})
                    if (value == 1) {
                      this.setState({deleteIntervals: true} as FormGoalState);
                      this.getIntervalData(R);
                    }
                    else {
                      R.GOALS = [];
                      this.setState({deleteIntervals: true} as FormGoalState);
                      this.setState({record: R});
                    }
                  }}
                />
              </FormInput>
            </div>
          </div>
        </div>
        {R.is_individual_goals == 1 ?
          <div className='card'>
            <div className='card-header flex flex-row justify-between cursor-pointer'
              onClick={(e) => this.setState({showIntervals: !this.state.showIntervals} as FormGoalState)}
            >
              <span className='text'>Individual Goals</span>
              <span className='icon'>
                <i className="fa-solid fa-chevron-down"></i>
              </span>
            </div>
            <div className={`card-body flex flex-row justify-center ${this.state.showIntervals ? "" : "hidden"}`}>
              <TableGoalValues
                uid={this.props.uid + "_table_goal_values"}
                data={{ data: R.GOALS }}
                descriptionSource='props'
                description={{
                  ui: {
                    showHeader: false,
                    showFooter: true
                  },
                  columns: {
                    interval: {type: "none", title: "Interval",
                      cellRenderer: ( table: TableGoalValues, data: any, options: any): JSX.Element => {
                        return (<>{data.key}</>)
                      }
                    },
                    value: {type: "float", title: this.state.record.metric == 1 ? "Value" : "Count"},
                  },
                  inputs: {
                    interval: {type: "none", title: "Interval",
                      cellRenderer: ( table: TableGoalValues, data: any, options: any): JSX.Element => {
                        return (<>{data.key}</>)
                      }
                    },
                    value: {type: "float", title: this.state.record.metric == 1 ? "Value" : "Count"},
                  }
                }}
                isUsedAsInput={true}
                isInlineEditing={this.state.isInlineEditing}
                onRowClick={() => this.setState({isInlineEditing: true})}
                onChange={(table: TableGoalValues) => {
                  this.updateRecord({ GOALS: table.state.data?.data });
                }}
                onDeleteSelectionChange={(table: TableGoalValues) => {
                  this.updateRecord({ GOALS: table.state.data?.data ?? [] });
                }}
              />
            </div>
          </div>
        : <></>}
        {showAdditional ?
          <div className='card'>
            <div className='card-body flex flex-row justify-center max-h-[35vh]'>
              <ReportGoal
                interval={[R.date_start, R.date_end]}
                user={R.id_user}
                frequency={R.frequency}
                metric={R.metric}
                goal={R.goal}
                goals={R.GOALS.length > 0 ? R.GOALS : null}
                idGoal={R.id}
                idPipeline={R.id_pipeline}
              />
            </div>
          </div>
        : <></>}
      </>
    )
  }
}