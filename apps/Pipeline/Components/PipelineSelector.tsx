import React, { Component } from "react";
import { getUrlParam } from "@hubleto/react-ui/core/Helper";
import TranslatedComponent from "@hubleto/react-ui/core/TranslatedComponent";
import Lookup from '@hubleto/react-ui/core/Inputs/Lookup';
import { ProgressBar } from 'primereact/progressbar';
import request from "@hubleto/react-ui/core/Request";

interface PipelineSelectorProps {
  idPipeline: number,
  idPipelineStep: number,
  onPipelineChange: (idPipeline: number, idPipelineStep: number) => void,
  onPipelineStepChange: (idPipelineStep: number, step: any) => void,
}

interface PipelineSelectorState {
  idPipeline: number,
  idPipelineStep: number,
  pipelines: Array<any>,
  changePipeline: boolean,
}

export default class PipelineSelector<P, S> extends TranslatedComponent<PipelineSelectorProps, PipelineSelectorState> {
  props: PipelineSelectorProps;
  state: PipelineSelectorState;

  translationContext: string = "HubletoApp\\Community\\Pipeline\\Loader::Components\\PipelineSelector";

  constructor(props: PipelineSelectorProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: PipelineSelectorProps) {
    return {
      pipelines: null,
      idPipeline: this.props.idPipeline,
      idPipelineStep: this.props.idPipelineStep,
      changePipeline: false,
    };
  }

  componentDidMount() {
    this.loadPipelines();
  }

  loadPipelines() {
    request.get(
      'pipeline/api/get-pipelines',
      {},
      (data: any) => { this.setState({ pipelines: data.pipelines }); }
    );
  }
  
  onPipelineChange(idPipeline: number) {
    this.setState({ idPipeline: idPipeline }, () => {
      if (this.props.onPipelineChange) {
        this.props.onPipelineChange(idPipeline, 0);
      }
    });
  }

  onPipelineStepChange(idPipelineStep: number, step: any) {
    this.setState({ idPipelineStep: idPipelineStep }, () => {
      if (this.props.onPipelineStepChange) {
        this.props.onPipelineStepChange(idPipelineStep, step);
      }
    });
  }

  render(): JSX.Element {
    const pipelines = this.state.pipelines;
    const steps = pipelines ? pipelines[this.state.idPipeline]?.STEPS : null;

    if (!pipelines) {
      return <ProgressBar mode="indeterminate" style={{ height: '8px' }}></ProgressBar>;
    }

    let stepBtnClass = "btn-light";

    return <>
      <div className='flex flex-row mt-2 flex-wrap'>
        {this.state.changePipeline ? <div className='flex gap-2 items-center'>
          <button className="btn btn-transparent btn-small ml-2" onClick={() => { this.setState({changePipeline: false}); }}>
            <span className="icon"><i className='fas fa-grip-lines'></i></span>
          </button>
          <div>
            Set pipeline to
          </div>
          <div className="input-body">
            <div className="hubleto component input"><div className="inner">
              <div className="input-element">
                {Object.keys(pipelines).map((idPipeline: any, key: any) => {
                  return <button
                    key={key}
                    className={"btn " + (this.state.idPipeline == idPipeline ? "btn-primary" : "btn-transparent")}
                    onClick={() => { this.onPipelineChange(idPipeline); }}
                  ><span className="text">{pipelines[idPipeline]?.name}</span></button>
                })}
              </div>
            </div></div>
          </div>
        </div> : <div className='flex gap-2'>
          <div>
            <button className="btn btn-transparent btn-small ml-2" onClick={() => { this.setState({changePipeline: true}); }}>
              <span className="icon"><i className='fas fa-grip-lines'></i></span>
            </button>
          </div>
          <div>
            {steps && steps.length > 0 ?
              steps.map((s, i) => {
                if (stepBtnClass == "btn-primary") stepBtnClass = "btn-transparent";
                else if (s.id == this.state.idPipelineStep) stepBtnClass = "btn-primary";
              return <button
                  key={i}
                  onClick={() => this.onPipelineStepChange(s.id, s)}
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
              : <p className='w-full text-center'>Pipeline has no steps.</p>
            }
          </div>
        </div>}
      </div>
    </>;
  }
}
