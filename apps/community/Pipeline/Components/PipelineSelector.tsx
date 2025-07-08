import React, { Component } from "react";
import { getUrlParam } from "adios/Helper";
import TranslatedComponent from "adios/TranslatedComponent";
import Lookup from 'adios/Inputs/Lookup';
import { ProgressBar } from 'primereact/progressbar';
import request from "adios/Request";

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

    console.log('pipelines', pipelines);
    console.log('steps', steps);
    console.log('state', this.state);

    let stepBtnClass = "btn-light";

    return <>
      <div className='card mt-2'>
        <div className='card-header'>
          <div className="input-body">
            <div className="adios component input"><div className="inner">
              <div className="input-element">
                {Object.keys(pipelines).map((idPipeline: any) => {
                  return <button
                    className={"btn " + (this.state.idPipeline == idPipeline ? "btn-primary" : "btn-transparent")}
                    onClick={() => { this.onPipelineChange(idPipeline); }}
                  ><span className="text">{pipelines[idPipeline].name}</span></button>
                })}
              </div>
            </div></div>
          </div>
          Pipeline
        </div>
        <div className='card-body'>
          <div className='flex flex-row gap-2 mt-2 flex-wrap'>
            {steps && steps.length > 0 ?
              steps.map((s, i) => {
                if (stepBtnClass == "btn-primary") stepBtnClass = "btn-transparent";
                else if (s.id == this.state.idPipelineStep) stepBtnClass = "btn-primary";
                return <>
                  <button
                    key={i}
                    onClick={() => this.onPipelineStepChange(s.id, s)}
                    className={`btn ${stepBtnClass}`}
                    style={{borderLeft: '1em solid ' + s.color}}
                  >
                    <div className='text text-center w-full flex'>
                      <span className='align-self-center'>
                        {s.name}
                        {s.probability ? <small className='whitespace-nowrap ml-2'>({s.probability} %)</small> : null}
                      </span>
                    </div>
                  </button>
                  {i+1 == steps.length ? null : <i className='fas fa-angles-right self-center text-gray-800 text-xs'></i>}
                </>;
              })
              : <p className='w-full text-center'>Pipeline has no steps.</p>
            }
          </div>
        </div>
      </div>
    </>;
  }
}
