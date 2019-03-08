import numpy as np

def sigmoid(x):
    return 1.0/(1+ np.exp(-x))

def sigmoid_derivative(x):
    return x * (1.0 - x)

class InputNeuron:
    def __init__(self, x):
        self.input      = x
        self.weights1   = np.random.rand(3, 4) 
        self.weights2   = np.random.rand(4, 1)                
        self.alpha 	= 0.05
        self.beta 	= 1 - self.alpha
        self.output     = np.zeros((4,1))
        self.p_weights1 = 0
        self.p_weights2 = 0

    def feedforward(self):
        self.layer1 = sigmoid(np.dot(self.input, self.weights1))
        self.output = sigmoid(np.dot(self.layer1, self.weights2))

    def backprop(self, d, w):
        # application of the chain rule to find derivative of the loss function with respect to weights2 and weights1
        error       = np.dot(d, w)
        d_weights2  = np.dot(self.layer1, (2*(error).T * sigmoid_derivative(self.output)))
        error       = np.dot(d_weights2, self.weights2.T)
        d_weights1  = np.dot(self.input.T,  (np.dot(2*(error) * sigmoid_derivative(self.output), self.weights2) 
                        * sigmoid_derivative(self.layer1)))

        # update the weights with the derivative (slope) of the loss function
        self.weights1 	+= d_weights1*self.alpha + self.p_weights1*self.beta
        self.weights2 	+= d_weights2*self.alpha + self.p_weights2*self.beta

        # update the values of previous deltas
        self.p_weights1 = d_weights1
        self.p_weights2 = d_weights2

class OutputNeuron:
    def __init__(self, x, y):
        self.input      = x
        self.weights    = np.random.rand(4, 4)
        self.alpha 	= 0.05
        self.beta 	= 1 - self.alpha
        self.y          = y
        self.output     = np.zeros(self.y.shape)
        self.p_weights  = 0

    def feedforward(self, x):
        self.input      = x
        self.output     = sigmoid(np.dot(x.T, self.weights))

    def backprop(self):
        # application of the chain rule to find derivative of the loss function with respect to weights2 and weights1
        d_weights = np.dot(self.input.T, (2*(self.y - self.output) * sigmoid_derivative(self.output)))
        print(d_weights)

        # update the weights with the derivative (slope) of the loss function
        self.weights 	+= d_weights*self.alpha + self.p_weights*self.beta

        # update the values of previous deltas
        self.p_weights = d_weights

if __name__ == "__main__":
    X = np.array([[0,0,1],
                  [0,1,1],
                  [1,0,1],
                  [1,1,1]])
    y = np.array([[0],[1],[1],[0]])

    input_neuron    = InputNeuron(X)
    output_neuron   = OutputNeuron(input_neuron.output, y)

    for i in range(1500):
        input_neuron.feedforward()
        output_neuron.feedforward(input_neuron.output)
        output_neuron.backprop()
        input_neuron.backprop(output_neuron.p_weights, output_neuron.weights)

print(output_neuron.output.T)
