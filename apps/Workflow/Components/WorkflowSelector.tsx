import React, { Component } from "react";
import { getUrlParam } from "@hubleto/react-ui/core/Helper";
import TranslatedComponent from "@hubleto/react-ui/core/TranslatedComponent";
import Lookup from '@hubleto/react-ui/core/Inputs/Lookup';
import { ProgressBar } from 'primereact/progressbar';
import request from "@hubleto/react-ui/core/Request";

interface WorkflowSelectorProps {
  parentForm: any,
  onWorkflowChange?: (idWorkflow: number, idWorkflowStep: number) => void,
  onWorkflowStepChange?: (idWorkflowStep: number, step: any) => void,
}

interface WorkflowSelectorState {
  workflows: Array<any>,
  history: Array<any>,
  changeWorkflow: boolean,
}

export default class WorkflowSelector<P, S> extends TranslatedComponent<WorkflowSelectorProps, WorkflowSelectorState> {
  props: WorkflowSelectorProps;
  state: WorkflowSelectorState;

  translationContext: string = 'Hubleto\\App\\Community\\Workflow\\Loader\\Loader';
  translationContextInner: string = 'Components\\WorkflowSelector';

  constructor(props: WorkflowSelectorProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: WorkflowSelectorProps) {
    return {
      workflows: null,
      history: null,
      changeWorkflow: false,
    };
  }

  componentDidMount() {
    this.loadData();
  }

  loadData(onSuccess?: any) {
    const R = this.props.parentForm.state.record ?? {};

    request.post(
      'workflow/api/get-workflows',
      {
        model: this.props.parentForm.props.model,
        recordId: R.id,
      },
      {},
      (data: any) => {
        this.props.parentForm.updateRecord({...this.props.parentForm.state.record, WORKFLOW_HISTORY: data.history})
        this.setState({ workflows: data.workflows, history: data.history });
        if (onSuccess) onSuccess();
      }
    );
  }
  
  onWorkflowChange(idWorkflow: number) {
    this.setState({ idWorkflow: idWorkflow, changeWorkflow: false }, () => {
      this.props.parentForm.updateRecord({id_workflow: idWorkflow}, () => {
        this.loadData(() => {
          if (this.props.onWorkflowChange) {
            this.props.onWorkflowChange(idWorkflow, 0);
          }
        });
      });
    });
  }

  onWorkflowStepChange(idWorkflowStep: number, step: any) {
    this.setState({ idWorkflowStep: idWorkflowStep }, () => {
      this.props.parentForm.updateRecord({id_workflow_step: idWorkflowStep}, () => {
        if (this.props.onWorkflowStepChange) {
          this.props.onWorkflowStepChange(idWorkflowStep, step);
        }
      });
    });
  }

  render(): JSX.Element {
    if (!this.state.workflows) {
      return <ProgressBar mode="indeterminate" style={{ height: '8px' }}></ProgressBar>;
    }

    const R = this.props.parentForm.state.record ?? {};
    const workflows = this.state.workflows;
    const history = this.state.history.filter((item) => item.id_workflow_step == R.id_workflow_step);
    const steps = workflows ? workflows[R.id_workflow]?.STEPS : null;

    // console.log(this.state.history, history);

    let stepBtnClass = "btn-light";

    return <>
      <div className='flex flex-row flex-wrap'>
        {this.state.changeWorkflow ? <div className='flex gap-2 items-center'>
          <div>
            Set workflow to
          </div>
          <div className="input-body">
            <div className="hubleto component input"><div className="inner">
              <div className="input-element">
                {Object.keys(workflows).map((idWorkflow: any, key: any) => {
                  return <button
                    key={key}
                    className={"btn " + (R.id_workflow == idWorkflow ? "btn-primary" : "btn-transparent")}
                    onClick={() => { this.onWorkflowChange(idWorkflow); }}
                  ><span className="text">{workflows[idWorkflow]?.name}</span></button>
                })}
              </div>
            </div></div>
          </div>
        </div> : <div className='flex gap-2'>
          <div className='flex flex-col'>
            <div className='flex items-center flex-col items-start'>
              {steps && steps.length > 0 ? <>
                <div>
                  {steps.map((s, i) => {
                    if (stepBtnClass == "btn-primary") stepBtnClass = "btn-transparent";
                    else if (s.id == R.id_workflow_step) stepBtnClass = "btn-primary";
                    
                    return <button
                      key={i}
                      onClick={() => this.onWorkflowStepChange(s.id, s)}
                      className={`btn btn-small ${stepBtnClass} border-none rounded-none`}
                    >
                      <div
                        className="icon p-0"
                        style={{
                          borderTop: '1em solid transparent',
                          borderBottom: '1em solid transparent',
                          borderLeft: '1em solid ' + s.color
                        }}
                      >
                      </div>
                      <div className='text'>
                        {s.name}
                        {/* {s.probability ? <small className='whitespace-nowrap ml-2'>({s.probability} %)</small> : null} */}
                      </div>
                    </button>;
                  })}
                </div>
                <div className='text-xs text-gray-400 flex gap-2'>
                  {history[0] ? <>Last update: {history[0].datetime_change} by {history[0].USER?.nick ?? 'unknown'}</> : null}
                  <a href='#' onClick={() => { this.setState({changeWorkflow: true}); }}>
                    <span className="text">{this.translate('Change workflow')}</span>
                  </a>
                </div>
              </> : <div>
                <button
                  className='btn btn-primary-outline btn-small'
                  onClick={() => { this.setState({changeWorkflow: true}); }}
                >
                  <span className='icon'><i className='fas fa-timeline'></i></span>
                  <span className="text">{this.translate('Change workflow')}</span>
                </button>
              </div>}
            </div>
          </div>
        </div>}
      </div>
    </>;
  }
}

export function updateFormWorkflowByTag(form: any, tag: string, onsuccess: any) {
  request.post(
    'workflow/api/get-workflow-step-by-tag',
    { idWorkflow: form.state.record.id_workflow, tag: tag },
    {},
    (result: any) => {
      form.updateRecord({id_workflow_step: result.id}, () => {
        if (onsuccess) onsuccess();
      });
    }
  );
}