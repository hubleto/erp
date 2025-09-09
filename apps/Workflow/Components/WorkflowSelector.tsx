import React, { Component } from "react";
import { getUrlParam } from "@hubleto/react-ui/core/Helper";
import TranslatedComponent from "@hubleto/react-ui/core/TranslatedComponent";
import Lookup from '@hubleto/react-ui/core/Inputs/Lookup';
import { ProgressBar } from 'primereact/progressbar';
import request from "@hubleto/react-ui/core/Request";

interface WorkflowSelectorProps {
  idWorkflow: number,
  idWorkflowStep: number,
  onWorkflowChange: (idWorkflow: number, idWorkflowStep: number) => void,
  onWorkflowStepChange: (idWorkflowStep: number, step: any) => void,
}

interface WorkflowSelectorState {
  workflows: Array<any>,
  changeWorkflow: boolean,
}

export default class WorkflowSelector<P, S> extends TranslatedComponent<WorkflowSelectorProps, WorkflowSelectorState> {
  props: WorkflowSelectorProps;
  state: WorkflowSelectorState;

  translationContext: string = "Hubleto\\App\\Community\\Workflow\\Loader::Components\\WorkflowSelector";

  constructor(props: WorkflowSelectorProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: WorkflowSelectorProps) {
    return {
      workflows: null,
      changeWorkflow: false,
    };
  }

  componentDidMount() {
    this.loadWorkflows();
  }

  loadWorkflows() {
    request.get(
      'workflow/api/get-workflows',
      {},
      (data: any) => { this.setState({ workflows: data.workflows }); }
    );
  }
  
  onWorkflowChange(idWorkflow: number) {
    this.setState({ idWorkflow: idWorkflow }, () => {
      if (this.props.onWorkflowChange) {
        this.props.onWorkflowChange(idWorkflow, 0);
      }
    });
  }

  onWorkflowStepChange(idWorkflowStep: number, step: any) {
    this.setState({ idWorkflowStep: idWorkflowStep }, () => {
      if (this.props.onWorkflowStepChange) {
        this.props.onWorkflowStepChange(idWorkflowStep, step);
      }
    });
  }

  render(): JSX.Element {
    const workflows = this.state.workflows;
    const steps = workflows ? workflows[this.props.idWorkflow]?.STEPS : null;

    if (!workflows) {
      return <ProgressBar mode="indeterminate" style={{ height: '8px' }}></ProgressBar>;
    }

    let stepBtnClass = "btn-light";

    return <>
      <div className='flex flex-row mt-2 flex-wrap'>
        {this.state.changeWorkflow ? <div className='flex gap-2 items-center'>
          <button className="btn btn-transparent btn-small ml-2" onClick={() => { this.setState({changeWorkflow: false}); }}>
            <span className="icon"><i className='fas fa-grip-lines'></i></span>
          </button>
          <div>
            Set workflow to
          </div>
          <div className="input-body">
            <div className="hubleto component input"><div className="inner">
              <div className="input-element">
                {Object.keys(workflows).map((idWorkflow: any, key: any) => {
                  return <button
                    key={key}
                    className={"btn " + (this.props.idWorkflow == idWorkflow ? "btn-primary" : "btn-transparent")}
                    onClick={() => { this.onWorkflowChange(idWorkflow); }}
                  ><span className="text">{workflows[idWorkflow]?.name}</span></button>
                })}
              </div>
            </div></div>
          </div>
        </div> : <div className='flex gap-2'>
          <div>
            <button className="btn btn-transparent btn-small ml-2" onClick={() => { this.setState({changeWorkflow: true}); }}>
              <span className="icon"><i className='fas fa-grip-lines'></i></span>
            </button>
          </div>
          <div>
            {steps && steps.length > 0 ?
              steps.map((s, i) => {
                if (stepBtnClass == "btn-primary") stepBtnClass = "btn-transparent";
                else if (s.id == this.props.idWorkflowStep) stepBtnClass = "btn-primary";
              return <button
                  key={i}
                  onClick={() => this.onWorkflowStepChange(s.id, s)}
                  className={`btn ${stepBtnClass} border-none rounded-none`}
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
              })
              : <p className='w-full text-center'>Workflow has no steps.</p>
            }
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