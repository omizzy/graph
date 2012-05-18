<?php
class AlgorithmSpMooreBellmanFord extends AlgorithmSp{
	/**
	 * 
	 * 
	 * @param Edge $edge
	 * @param Vertex $fromVertex
	 * @param Vertex $toVertex
	 * @param array[int] $totalCostOfCheapestPathTo
	 * @param array[Vertex] $predecessorVertexOfCheapestPathTo
	 * 
	 * @return boolean
	 */
	private function doStep(& $edge, & $fromVertex, & $toVertex, & $totalCostOfCheapestPathTo, & $predecessorVertexOfCheapestPathTo){
		$isCheaper = false;
		
		if (isset($totalCostOfCheapestPathTo[$fromVertex->getId()])){			//If the fromVertex already has a path
			$newCost = $totalCostOfCheapestPathTo[$fromVertex->getId()] + $edge->getWeight(); //New possible costs of this path
	
			if (! isset($totalCostOfCheapestPathTo[$toVertex->getId()])				//No path has been found yet
					|| $totalCostOfCheapestPathTo[$toVertex->getId()] > $newCost){		//OR this path is cheaper than the old path
	
				$isCheaper = true;
				$totalCostOfCheapestPathTo[$toVertex->getId()] = $newCost;
				$predecessorVertexOfCheapestPathTo[$toVertex->getId()] = $fromVertex;
			}
		}
		
		return $isCheaper;
	}
	
	private function bigStep(&$edges,&$totalCostOfCheapestPathTo,&$predecessorVertexOfCheapestPathTo){
		$changed = false;
		foreach ($edges as $edge){												//check for all edges
			foreach($edge->getTargetVertices() as $toVertex){						//check for all "ends" of this edge (or for all targetes)
				$fromVertex = $edge->getVertexFromTo($toVertex);
	
				if($this->doStep($edge, $fromVertex, $toVertex, $totalCostOfCheapestPathTo, $predecessorVertexOfCheapestPathTo)){	//do normal step
					$changed = true;
				}
			}
		}
		return $changed;
	}
	
	/**
	 * Calculate the Moore-Bellman-Ford-Algorithm and get all edges on shortest path for this vertex
	 * 
	 * @return array[Edge]
	 * @throws Exception if there is a negative cycle
	 */
	public function getEdges(){
		$totalCostOfCheapestPathTo  = array($this->startVertex->getId() => 0);		    //start node distance
		
		$predecessorVertexOfCheapestPathTo  = array($this->startVertex->getId() => $this->startVertex);	//predecessor
		
		$numSteps = $this->startVertex->getGraph()->getNumberOfVertices() - 1; // repeat (n-1) times
		$edges = $this->startVertex->getGraph()->getEdges();
		$changed = true;
		for ($i = 0; $i < $numSteps && $changed; ++$i){						//repeat n-1 times
			$changed = $this->bigStep($edges,$totalCostOfCheapestPathTo,$predecessorVertexOfCheapestPathTo);
		}
		
		//algorithm is done, build graph
		$returnEdges = $this->getEdgesCheapestPredecesor($predecessorVertexOfCheapestPathTo);
		
		//Check for negative cycles (only if last step didn't already finish anyway)
	    if($changed && $this->bigStep($edges,$totalCostOfCheapestPathTo,$predecessorVertexOfCheapestPathTo)){ // something is still changing...
		    throw new Exception("Negative Cycle");
		}
		
		return $returnEdges;
	}
}
